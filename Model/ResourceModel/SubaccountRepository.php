<?php

namespace Cminds\MultiUserAccounts\Model\ResourceModel;

use Cminds\MultiUserAccounts\Api\Data\SubaccountInterface;
use Cminds\MultiUserAccounts\Api\SubaccountRepositoryInterface;
use Cminds\MultiUserAccounts\Model\Permission;
use Cminds\MultiUserAccounts\Model\SubaccountFactory;
use Cminds\MultiUserAccounts\Model\SubaccountRegistry;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\InputException;

/**
 * Cminds MultiUserAccounts subaccount repository resource model.
 *
 * @category    Cminds
 * @package     Cminds_MultiUserAccounts
 * @author      Piotr Pierzak <piotr@cminds.com>
 */
class SubaccountRepository implements SubaccountRepositoryInterface
{
    /**
     * @var SubaccountFactory
     */
    private $subaccountFactory;

    /**
     * @var SubaccountRegistry
     */
    private $subaccountRegistry;

    /**
     * @var Permission
     */
    private $permission;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * Object initialization.
     *
     * @param SubaccountFactory  $subaccountFactory
     * @param SubaccountRegistry $subaccountRegistry
     * @param Permission         $permission
     * @param ManagerInterface   $eventManager
     */
    public function __construct(
        SubaccountFactory $subaccountFactory,
        SubaccountRegistry $subaccountRegistry,
        Permission $permission,
        ManagerInterface $eventManager
    ) {
        $this->subaccountFactory = $subaccountFactory;
        $this->subaccountRegistry = $subaccountRegistry;
        $this->permission = $permission;
        $this->eventManager = $eventManager;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param SubaccountInterface $subaccountDataObject
     *
     * @return SubaccountInterface
     * @throws \Zend_Validate_Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Exception
     */
    public function save(SubaccountInterface $subaccountDataObject)
    {
        $this->validate($subaccountDataObject);

        if ($subaccountDataObject->getId()) {
            $subaccountModel = $this->subaccountRegistry
                ->retrieve($subaccountDataObject->getId());
        } else {
            $subaccountModel = $this->subaccountFactory->create();
        }

        $origSubaccountDataObject = $subaccountModel->getDataModel();
        $this->permission->recalculatePermission($subaccountDataObject);

        $subaccountModel
            ->updateData($subaccountDataObject)
            ->save();

        $this->subaccountRegistry->push($subaccountModel);
        $savedSubaccount = $this->getById($subaccountModel->getId());

        $this->eventManager->dispatch(
            'subaccount_save_after_data_object',
            [
                'subaccount_data_object' => $savedSubaccount,
                'orig_subaccount_data_object' => $origSubaccountDataObject,
            ]
        );

        return $savedSubaccount;
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $customerId
     *
     * @return SubaccountInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId($customerId)
    {
        $subaccountModel = $this->subaccountRegistry
            ->retrieveByCustomerId($customerId);

        return $subaccountModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $subaccountId
     *
     * @return SubaccountInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($subaccountId)
    {
        $subaccountModel = $this->subaccountRegistry->retrieve($subaccountId);

        return $subaccountModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param SubaccountInterface $subaccount
     *
     * @return bool
     * @throws \Exception
     */
    public function delete(SubaccountInterface $subaccount)
    {
        return $this->deleteById($subaccount->getId());
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param int $subaccountId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Exception
     */
    public function deleteById($subaccountId)
    {
        $this->subaccountRegistry
            ->retrieve($subaccountId)
            ->delete();

        $this->subaccountRegistry->remove($subaccountId);

        return true;
    }

    /**
     * Validate subaccount data.
     *
     * @param SubaccountInterface $subaccount
     *
     * @return SubaccountRepository
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Zend_Validate_Exception
     */
    private function validate(SubaccountInterface $subaccount)
    {
        $exception = new InputException();

        $customerId = trim($subaccount->getCustomerId());
        if (!\Zend_Validate::is($customerId, 'NotEmpty')) {
            $exception->addError(
                __(
                    InputException::REQUIRED_FIELD,
                    ['fieldName' => 'Customer ID']
                )
            );
        }

        $parentCustomerId = trim($subaccount->getParentCustomerId());
        if (!\Zend_Validate::is($parentCustomerId, 'NotEmpty')) {
            $exception->addError(
                __(
                    InputException::REQUIRED_FIELD,
                    ['fieldName' => 'Parent Customer ID']
                )
            );
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }

        return $this;
    }
}
