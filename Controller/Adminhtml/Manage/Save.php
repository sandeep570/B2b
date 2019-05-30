<?php

namespace Cminds\MultiUserAccounts\Controller\Adminhtml\Manage;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterfaceFactory;
use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Controller\Adminhtml\AbstractManage;
use Cminds\MultiUserAccounts\Model\AccountManagement as CustomerAccountManagement;
use Cminds\MultiUserAccounts\Model\Permission;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Cminds MultiUserAccounts admin manage save controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Save extends AbstractManage
{
    /**
     * Subaccount transport repository object.
     *
     * @var SubaccountTransportRepositoryInterface
     */
    private $subaccountTransportRepository;

    /**
     * Subaccount transport factory object.
     *
     * @var SubaccountTransportInterfaceFactory
     */
    private $subaccountTransportDataFactory;

    /**
     * Custom account management object.
     *
     * @var CustomerAccountManagement
     */
    private $customerAccountManagement;

    /**
     * Customer registry object.
     *
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * Data object processor.
     *
     * @var DataObjectProcessor
     */
    private $dataProcessor;

    /**
     * Data object helper.
     *
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Permission object.
     *
     * @var Permission
     */
    private $permission;

    /**
     * Object initialization.
     *
     * @param Context                                $context
     * @param SubaccountTransportRepositoryInterface $subaccountTransportRepository
     * @param SubaccountTransportInterfaceFactory    $subaccountTransportDataFactory
     * @param CustomerAccountManagement              $customerAccountManagement
     * @param CustomerRegistry                       $customerRegistry
     * @param DataObjectProcessor                    $dataProcessor
     * @param DataObjectHelper                       $dataObjectHelper
     * @param Permission                             $permission
     */
    public function __construct(
        Context $context,
        SubaccountTransportRepositoryInterface $subaccountTransportRepository,
        SubaccountTransportInterfaceFactory $subaccountTransportDataFactory,
        CustomerAccountManagement $customerAccountManagement,
        CustomerRegistry $customerRegistry,
        DataObjectProcessor $dataProcessor,
        DataObjectHelper $dataObjectHelper,
        Permission $permission
    ) {
        $this->subaccountTransportRepository = $subaccountTransportRepository;
        $this->subaccountTransportDataFactory = $subaccountTransportDataFactory;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRegistry = $customerRegistry;
        $this->dataProcessor = $dataProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->permission = $permission;

        parent::__construct($context);
    }

    /**
     * Subaccount admin save action.
     *
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->isPost() === false) {
            return $resultRedirect->setPath('*/*/edit');
        }

        $subaccountData = $this->getRequest()->getParam('subaccount');
        $subaccountId = !empty($subaccountData['id'])
            ? $subaccountData['id']
            : null;

        try {
            $subaccountTransportDataObject = $this->extractSubaccount();

            $parentCustomerId = $subaccountTransportDataObject->getParentCustomerId();

            $savedSubaccountTransportDataObject = $this
                ->subaccountTransportRepository
                ->save($subaccountTransportDataObject);

            $subaccountId = $savedSubaccountTransportDataObject->getId();
            $this->changeSubaccountPassword($savedSubaccountTransportDataObject);

            if (!$subaccountTransportDataObject->getCustomerId()) {
                $customerModel = $this->customerRegistry
                    ->retrieve($savedSubaccountTransportDataObject->getCustomerId());
                $this->customerAccountManagement
                    ->sendSubaccountEmailConfirmation(
                        $customerModel->getDataModel()
                    );
            }

            $this->messageManager->addSuccessMessage(__('Subaccount has been saved.'));

            $this->_getSession()->setSubaccountFormData(null);

            return $resultRedirect->setPath(
                'customer/index/edit',
                [
                    '_secure' => true,
                    'id' => $parentCustomerId,
                ]
            );
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addErrorMessage($error->getMessage());
            }
            $this->_getSession()->setSubaccountFormData($subaccountData);
        } catch (AuthenticationException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->_getSession()->setSubaccountFormData($subaccountData);
        } catch (AlreadyExistsException $e) {
            $this->messageManager->addErrorMessage(
                __('Account with provided email address already exists.')
            );
            $this->_getSession()->setSubaccountFormData($subaccountData);
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('During subaccount save process error has occurred.')
            );
            $this->_getSession()->setSubaccountFormData($subaccountData);
        }

        if ($subaccountId) {
            return $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $subaccountId]
            );
        }

        return $resultRedirect->setPath(
            '*/*/add',
            ['parent_customer_id' => $parentCustomerId]
        );
    }

    /**
     * Change subaccount password.
     *
     * @param SubaccountTransportInterface $savedSubaccountTransportDataObject Subaccount
     *     transport data object.
     *
     * @return Save
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws InputException
     * @throws \Exception
     */
    private function changeSubaccountPassword(
        SubaccountTransportInterface $savedSubaccountTransportDataObject
    ) {
        $subaccountData = $this->getRequest()->getParam('subaccount');

        $newPass = !empty($subaccountData['password'])
            ? $subaccountData['password']
            : null;
        $confPass = !empty($subaccountData['password_confirmation'])
            ? $subaccountData['password_confirmation']
            : null;

        $customerSecureDataObject = $this->customerRegistry
            ->retrieveSecureData(
                $savedSubaccountTransportDataObject->getCustomerId()
            );

        /**
         * If subaccount already exist and it has set password hash we do
         * not require password change, so if password fields are empty
         * we're going to do nothing.
         */
        $hash = $customerSecureDataObject->getPasswordHash();
        if (!empty($hash)
            && empty($newPass)
            && empty($confPass)
            && $savedSubaccountTransportDataObject->getId()
        ) {
            return $this;
        }

        if ($newPass === '') {
            throw new InputException(__('Please enter new password.'));
        }

        if ($newPass !== $confPass) {
            throw new InputException(__('Confirm your new password.'));
        }

        $this->customerAccountManagement->changePassword(
            $savedSubaccountTransportDataObject->getEmail(),
            '',
            $newPass
        );

        return $this;
    }

    /**
     * Extract subaccount from request.
     *
     * @return SubaccountTransportInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function extractSubaccount()
    {
        $existingSubaccountTransportDataArray = $this->getExistingSubaccountData();

        $subaccountData = $this->getRequest()->getParam('subaccount');
        $subaccountTransportDataObject = $this->subaccountTransportDataFactory
            ->create();

        $subaccountData[$subaccountTransportDataObject::ADDITIONAL_INFORMATION]
            = json_encode($subaccountData[$subaccountTransportDataObject::ADDITIONAL_INFORMATION]);

        $this->dataObjectHelper->populateWithArray(
            $subaccountTransportDataObject,
            array_merge($existingSubaccountTransportDataArray, $subaccountData),
            '\Cminds\MultiUserAccounts\Api\Data\SubaccountInterface'
        );

        if (!isset($subaccountData[$subaccountTransportDataObject::IS_ACTIVE])) {
            $subaccountTransportDataObject->setIsActive(
                $subaccountTransportDataObject::NOT_ACTIVE_FLAG
            );
        }

        $parentCustomer = $this->customerRegistry->retrieve(
            $subaccountTransportDataObject->getParentCustomerId()
        );

        if ($subaccountTransportDataObject->getGroupId() !== $parentCustomer->getGroupId()) {
            $subaccountTransportDataObject->setGroupId(
                $parentCustomer->getGroupId()
            );
        }

        return $subaccountTransportDataObject;
    }

    /**
     * Retrieve existing subaccount data.
     *
     * @return array
     * @throws \Exception
     */
    private function getExistingSubaccountData()
    {
        $existingSubaccountTransportDataArray = [];

        $subaccountData = $this->getRequest()->getParam('subaccount');
        $subaccountId = !empty($subaccountData['id'])
            ? $subaccountData['id']
            : null;

        if ($subaccountId) {
            $existingSubaccountTransportDataObject = $this
                ->subaccountTransportRepository
                ->getById($subaccountId);

            /**
             * Reset subaccount permissions.
             * Will be overwritten by new permissions and recalculated
             * before subaccount entity save.
             */
            $existingSubaccountTransportDataObject->setPermission(0);
            $this->permission->loadSubaccountPermissions(
                $existingSubaccountTransportDataObject
            );

            $existingSubaccountTransportDataArray = $this->dataProcessor
                ->buildOutputDataArray(
                    $existingSubaccountTransportDataObject,
                    '\Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface'
                );
        }

        return $existingSubaccountTransportDataArray;
    }
}
