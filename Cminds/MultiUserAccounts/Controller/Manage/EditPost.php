<?php

namespace Cminds\MultiUserAccounts\Controller\Manage;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterfaceFactory;
use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Controller\AbstractManage;
use Cminds\MultiUserAccounts\Model\AccountManagement as CustomerAccountManagement;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Cminds\MultiUserAccounts\Model\Permission;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Session\Proxy as Session;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Cminds MultiUserAccounts manage edit post controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class EditPost extends AbstractManage
{
    /**
     * Session object.
     *
     * @var Session
     */
    private $session;

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
     * Validator object.
     *
     * @var Validator
     */
    private $formKeyValidator;

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
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Object initialization.
     *
     * @param Context                                $context
     * @param Session                                $customerSession
     * @param SubaccountTransportRepositoryInterface $subaccountTransportRepository
     * @param SubaccountTransportInterfaceFactory    $subaccountTransportDataFactory
     * @param Validator                              $formKeyValidator
     * @param DataObjectProcessor                    $dataProcessor
     * @param DataObjectHelper                       $dataObjectHelper
     * @param Permission                             $permission
     * @param CustomerAccountManagement              $customerAccountManagement
     * @param CustomerRegistry                       $customerRegistry
     * @param CustomerRepositoryInterface            $customerRepository
     * @param ModuleConfig                           $moduleConfig
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        SubaccountTransportRepositoryInterface $subaccountTransportRepository,
        SubaccountTransportInterfaceFactory $subaccountTransportDataFactory,
        Validator $formKeyValidator,
        DataObjectProcessor $dataProcessor,
        DataObjectHelper $dataObjectHelper,
        Permission $permission,
        CustomerAccountManagement $customerAccountManagement,
        CustomerRegistry $customerRegistry,
        CustomerRepositoryInterface $customerRepository,
        ModuleConfig $moduleConfig
    ) {
        $this->session = $customerSession;
        $this->subaccountTransportRepository = $subaccountTransportRepository;
        $this->subaccountTransportDataFactory = $subaccountTransportDataFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->dataProcessor = $dataProcessor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->permission = $permission;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerRegistry = $customerRegistry;
        $this->customerRepository = $customerRepository;
        $this->moduleConfig = $moduleConfig;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setPath('*/*/edit');
        }

        if ($this->getRequest()->isPost() === false) {
            return $resultRedirect->setPath('*/*/edit');
        }

        $customerId = $this->session->getCustomerData()->getId();
        $customer = $this->customerRepository->getById($customerId);

        $canManageSubaccounts = $customer->getCustomAttribute('can_manage_subaccounts');
        if ($canManageSubaccounts !== null) {
            $canManageSubaccounts = $canManageSubaccounts->getValue();
        } else {
            $canManageSubaccounts = $this->moduleConfig
                ->canParentAccountManageSubaccounts();
        }

        if ((bool)$canManageSubaccounts === false) {
            return $resultRedirect->setPath('*/*/index');
        }

        $subaccountId = $this->getRequest()->getParam('id');

        try {
            $subaccountTransportDataObject = $this->extractSubaccount();
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
            $url = $this->buildUrl('*/*/index', ['_secure' => true]);

            $this->getSession()->setSubaccountFormData(null);

            return $resultRedirect->setUrl($this->_redirect->success($url));
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addErrorMessage($error->getMessage());
            }
        } catch (AuthenticationException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (AlreadyExistsException $e) {
            $this->messageManager->addErrorMessage(
                __('Account with provided email address already exists.')
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('During subaccount save process error has occurred.')
            );
        }

        $this->getSession()->setSubaccountFormData(
            $this->getRequest()->getPostValue()
        );

        if ($subaccountId) {
            $redirectUrl = $this->buildUrl('*/*/edit', ['id' => $subaccountId]);
        } else {
            $redirectUrl = $this->buildUrl('*/*/add');
        }

        return $resultRedirect->setUrl($this->_redirect->error($redirectUrl));
    }

    /**
     * Change subaccount password.
     *
     * @param SubaccountTransportInterface $savedSubaccountTransportDataObject Subaccount
     *     transport data object.
     *
     * @return EditPost
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws InputException
     * @throws \Exception
     */
    private function changeSubaccountPassword(
        SubaccountTransportInterface $savedSubaccountTransportDataObject
    ) {
        $newPass = $this->getRequest()->getPost('password');
        $confPass = $this->getRequest()->getPost('password_confirmation');

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
     * Retrieve customer session object.
     *
     * @return Session
     */
    private function getSession()
    {
        return $this->session;
    }

    /**
     * Extract subaccount from request.
     *
     * @return SubaccountTransportInterface
     */
    private function extractSubaccount()
    {
        $existingSubaccountTransportDataArray = $this->getExistingSubaccountData();

        $subaccountData = $this->getRequest()->getParams();
        $subaccountTransportDataObject = $this->subaccountTransportDataFactory
            ->create();

        $subaccountData[$subaccountTransportDataObject::ADDITIONAL_INFORMATION]
            = json_encode($subaccountData[$subaccountTransportDataObject::ADDITIONAL_INFORMATION]);

        $this->dataObjectHelper->populateWithArray(
            $subaccountTransportDataObject,
            array_merge($existingSubaccountTransportDataArray, $subaccountData),
            '\Cminds\MultiUserAccounts\Api\Data\SubaccountInterface'
        );

        $parentCustomer = $this->getSession()->getCustomer();

        $subaccountTransportDataObject
            ->setParentCustomerId($parentCustomer->getId())
            ->setGroupId($parentCustomer->getGroupId());

        if (!isset($subaccountData[$subaccountTransportDataObject::IS_ACTIVE])) {
            // TODO here I need to change is active for customer attributue
            $subAccountModel = $this->customerRegistry->retrieve($existingSubaccountTransportDataArray['customer_id']);
            $subAccountModel->setCustomerIsActive(0);
            $subAccountModel->save();
            $subaccountTransportDataObject->setIsActive(
                $subaccountTransportDataObject::NOT_ACTIVE_FLAG
            );
        }

        if (isset($subaccountData[$subaccountTransportDataObject::TAXVAT])) {
            $subaccountTransportDataObject->setTaxvat(
                $subaccountData[$subaccountTransportDataObject::TAXVAT]
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
        $subaccountId = (int)$this->getRequest()->getParam('id');

        if ($subaccountId) {
            $existingSubaccountTransportDataObject = $this
                ->subaccountTransportRepository
                ->getById($subaccountId);

            $parentCustomerId = (int)$existingSubaccountTransportDataObject
                ->getParentCustomerId();
            $customerId = (int)$this->getSession()->getCustomerId();
            if ($parentCustomerId !== $customerId) {
                throw new \Exception(
                    __('You are not allowed for this operation.')
                );
            }

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

    /**
     * Return generated url to provided route.
     *
     * @param string $route Route string.
     * @param array  $params Params array.
     *
     * @return string
     */
    private function buildUrl($route = '', $params = [])
    {
        return $this->_url->getUrl($route, $params);
    }
}
