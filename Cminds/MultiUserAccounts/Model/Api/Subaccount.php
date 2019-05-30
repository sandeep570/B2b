<?php

namespace Cminds\MultiUserAccounts\Model\Api;

use Cminds\MultiUserAccounts\Api\ParentaccountInterface;
use Cminds\MultiUserAccounts\Api\Data\ApiParentAccountInterface;
use Cminds\MultiUserAccounts\Api\Data\ApiSubAccountInterface;
use Cminds\MultiUserAccounts\Api\SubaccountInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount as SubaccountResourceModel;
use Cminds\MultiUserAccounts\Model\ResourceModel\SubaccountRepository as SubaccountRepository;
use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Model\Permission;
use Cminds\MultiUserAccounts\Model\Import;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Cminds\MultiUserAccounts\Model\Api\Parentaccount;


/**
 * Class Parentaccount
 *
 * @package Cminds\MultiUserAccounts\Model\API
 */
class Subaccount implements SubaccountInterface
{

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;


    protected $addressRepository;

    protected $customerRepository;

    protected $subaccountResourceModel;

    protected $subaccountRepository;

    protected $subaccountTransportRepository;

    protected $permission;

    protected $parentaccountModel;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var Import
     */
    protected $import;

    /**
     * Variable disables validation parent customer id validation when subaccount is promoted
     *
     * @var bool
     */
    private $processValidate = false;

    public function __construct(
        AddressRepositoryInterface $addressRepository,
        CustomerRepositoryInterface $customerRepository,
        SubaccountResourceModel $subaccountResourceModel,
        SubaccountRepository $subaccountRepository,
        SubaccountTransportRepositoryInterface $subaccountTransportRepository,
        Permission $permission,
        Import $import,
        CustomerRegistry $customerRegistry,
        DataObjectFactory $dataObjectFactory,
        Parentaccount $parentaccountModel
    )
    {
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
        $this->subaccountResourceModel = $subaccountResourceModel;
        $this->subaccountRepository = $subaccountRepository;
        $this->subaccountTransportRepository = $subaccountTransportRepository;
        $this->permission = $permission;
        $this->import = $import;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->customerRegistry = $customerRegistry;
        $this->parentaccountModel = $parentaccountModel;
    }

    public function getAllSubs($parentId)
    {
        try {
            $customer = $this->getCustomerById($parentId);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(
                __('Customer with provided id does not exist or is not a parent account')
            );
        }

        try {
            $subaccounts = $this->subaccountResourceModel->getSubaccountIdsByParentCustomerId($parentId);
            if (count($subaccounts) === 0) {
                throw new NoSuchEntityException(
                    __('Customer with provided id doesn\'t have any sub accounts.')
                );
            }
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(
                __('Parent Account does not have any sub accounts.')
            );
        }

        if (count($this->subaccountResourceModel->getSubaccountIdsByParentCustomerId($parentId)) > 0) {
            $subAccountData = [];
            $addressData = [];
            $customerData = [];

            $subArray = $this->subaccountResourceModel->getSubaccountIdsByParentCustomerId($parentId);

            foreach ($subArray as $sub) {
                $subId = $this->subaccountRepository->getById($sub)->getCustomerId();
                $subAccount = $this->getCustomerById($subId);

                if ($sub) {
                    $existingSubaccountTransportDataObject = $this->getSubAccountTransportDataObject($sub);
                }

                foreach ($subAccount->getAddresses() as $subAddress) {
                    if ($subAddress->isDefaultShipping()) {
                        $subAccountAddress = $subAddress;
                    }
                }

                if (!isset($subAccountAddress)) {
                    $payload = [
                        "city" => "",
                        "country_id" => "",
                        "region" => "",
                        "postcode" => "",
                        "telephone" => "",
                        "street" => [],
                    ];
                    $subAccountAddress = new \Magento\Framework\DataObject($payload);
                }

                $streetSubArray = $this->getStreetArray($subAccountAddress);

                $subAccountData[$subAccount->getEmail()] = [
                    'id' => $subAccount->getId(),
                    'parent_email' => $customer->getEmail(),
                    'firstname' => $subAccount->getFirstname(),
                    'lastname' => $subAccount->getLastname(),
                    'email' => $subAccount->getEmail(),
                    'website_id' => $subAccount->getWebsiteId(),
                    'group_id' => $subAccount->getGroupId(),
                    'prefix' => $subAccount->getPrefix(),
                    'middlename' => $subAccount->getMiddlename(),
                    'suffix' => $subAccount->getSuffix(),
                    'dob' => $subAccount->getDob(),
                    'taxvat' => $subAccount->getTaxvat(),
                    'gender' => $subAccount->getGender(),
                    'is_active' => $subAccount->getCustomAttribute('customer_is_active')->getValue(),
                    'company' => '',
                    'city' => $subAccountAddress->getCity(),
                    'country_id' => $subAccountAddress->getCountryId(),
                    'region' => $subAccountAddress->getRegionId(),
                    'postcode' => $subAccountAddress->getPostcode(),
                    'telephone' => $subAccountAddress->getTelephone(),
                    'fax' => '',
                    'vat_id' => '',
                    'street_1' => $streetSubArray[0],
                    'street_2' => $streetSubArray[1],
                    'account_data_modification_permission' =>
                        (int)$existingSubaccountTransportDataObject->getAccountDataModificationPermission(),
                    'account_order_history_view_permission' =>
                        (int)$existingSubaccountTransportDataObject->getAccountOrderHistoryViewPermission(),
                    'checkout_order_create_permission' =>
                        (int)$existingSubaccountTransportDataObject->getCheckoutOrderCreatePermission(),
                    'checkout_order_approval_permission' =>
                        (int)$existingSubaccountTransportDataObject->getCheckoutOrderApprovalPermission(),
                    'checkout_cart_view_permission' =>
                        (int)$existingSubaccountTransportDataObject->getCheckoutCartViewPermission(),
                    'checkout_view_permission' =>
                        (int)$existingSubaccountTransportDataObject->getCheckoutViewPermission(),
                    'checkout_order_placed_notification_permission' =>
                        (int)$existingSubaccountTransportDataObject->getCheckoutOrderPlacedNotificationPermission(),
                    'force_usage_parent_company_name_permission' =>
                        (int)$existingSubaccountTransportDataObject->getForceUsageParentCompanyNamePermission(),
                    'force_usage_parent_vat_permission' =>
                        (int)$existingSubaccountTransportDataObject->getForceUsageParentVatPermission(),
                    'force_usage_parent_addresses_permission' =>
                        (int)$existingSubaccountTransportDataObject->getForceUsageParentAddressesPermission(),
                ];
            }

            $result = [
                $subAccountData,
            ];
        } else {
            $result[] = [
                'code' => 404,
                'message' => 'Customer with this ID isn\'t Parent Account / has no related subaccounts'
            ];
        }
        return $result;
    }


    /**
     * Loads customer object from customer Id
     *
     * @param integer $id Customer ID.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    protected function getCustomerById($id)
    {
        return $this->customerRepository->getById($id);
    }


    /**
     * Get Sub account data
     *
     * @param int $parentId
     * @param int $subAId
     * @return array
     */
    public function getById($parentId, $subAId)
    {
        try {
            $subaccount = $this->subaccountRepository->getByCustomerId($subAId);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(
                __('Sub account with provided id doesn\'t exist.')
            );
        }

        if ($this->processValidate) {
            if ($subaccount->getParentCustomerId() !== $parentId) {
                throw new NoSuchEntityException(
                    __('Sub account with provided id doesn\'t belong to parent id.')
                );
            }
        }

        $this->processValidate = false;
        $result = [];
        if (count($this->subaccountResourceModel->getSubaccountIdsByParentCustomerId($parentId)) > 0) {
            $allSubs = $this->getAllSubs($parentId);
            foreach ($allSubs as $array) {
                foreach ($array as $sub) {
                    if ($sub['id'] == $subAId) {
                        $result[] = $sub;
                        return $result;
                    }
                }
            }
            if (empty($result)) {
                throw new NoSuchEntityException(
                    __('Sub account with provided id doesn\'t exist or is not a child of provided parent id.')
                );
            }

        } else {
            throw new NoSuchEntityException(
                __('Customer with provided id isn\'t Parent Account or has no related subaccounts')
            );
        }
        return $result;
    }


    /**
     * Create new sub account
     *
     * @param int $parentId
     * @param string[] $customer
     * @return array
     */
    public function create($parentId, array $customer)
    {
        try {
            $parent = $this->getCustomerById($parentId);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(
                __('Customer with provided parent id doesn\'t exist.')
            );
        }

        try {
            $this->import
                ->initSourceProcessor(Import::SOURCE_API)
                ->setApiData($customer)
                ->setUpdateFlag(false)
                ->setParentId($parentId)
                ->setParentEmail();
            $this->import->process();
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Sub account has been not created. Details: '.$e->getMessage().'.')
            );
        }

        $customer = $this->customerRegistry->retrieveByEmail($customer['email']);

        return $this->getById($parentId, $customer->getId());
    }


    /**
     * Update sub account by it's id
     *
     * @param int $parentId
     * @param ApiSubAccountInterface $customer
     * @param int $subId
     * @return array
     */
    public function updateById($parentId, ApiSubAccountInterface $customer, $subId)
    {
        $downgradeFlag = false;
        if( $customer->getDowngrade() !== null && $customer->getDowngrade() === true ){
            $downgradeFlag = true;
        }
        if ($customer->getParentEmail()) {
            $downgradeFlag = true;
        }
        if (!$downgradeFlag) {
            try {
                $subaccount = $this->subaccountRepository->getByCustomerId($subId);
            } catch (LocalizedException $e) {
                throw new LocalizedException(
                    __('Customer with provided id is not a sub account or does not exist')
                );
            }
            if ($subaccount->getParentCustomerId() !== $parentId) {
                throw new NoSuchEntityException(
                    __('Customer Parent Account doesn\'t exist or is not a parent of sub account')
                );
            }
        }

        try {
            if ($downgradeFlag) {
                $parentEmail = $customer->getParentEmail();
                $parent = $this->customerRepository->get($parentEmail);
                $this->import->setUpdateFlag();
                $this->import->setCustomerId($subId);
                $this->import->setParentId($parentId);
                $this->import->setLinkFlag(true);
                $this->import
                    ->initSourceProcessor(Import::SOURCE_API)
                    ->setApiData($customer)
                    ->setUpdateFlag(true)
                    ->setParentId($parentId)
                    ->setParentEmail()
                    ->setSubId($subId);
                $this->import->process();
            } else {
                $this->import->setUpdateFlag();
                $this->import->setCustomerId($subId);
                $this->import->setParentId($parentId);
                $this->import
                    ->initSourceProcessor(Import::SOURCE_API)
                    ->setApiData($customer)
                    ->setUpdateFlag(true)
                    ->setParentId($parentId)
                    ->setParentEmail()
                    ->setSubId($subId);
                $this->import->process();
            }
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Sub account has been not updated. Details: '.$e->getMessage().'.')
            );
        }
        if ($this->import->getWasLinked()){
            $newParent = $this->parentaccountModel->getById($parentId);
            return $newParent;
        }
        if ($this->import->getWasPromoted()) {
            $this->processValidate = true;
            return $this->getAllSubs($subId);
        } else {
            $this->processValidate = true;
            return $this->getById($parentId, $subId);
        }
    }


    /**
     * Delete sub account by id.
     *
     * @param int $parentId
     * @param int $subId
     * @return array
     */
    public function deleteById($parentId, $subId)
    {
        $result = [];
        $subIdsArray = $this->subaccountResourceModel->getSubaccountIdsByParentCustomerId($parentId);
        if (count($subIdsArray) > 0) {
            try {
                $subaccount = $this->subaccountTransportRepository->getByCustomerId($subId);
            } catch (NoSuchEntityException $e) {
                throw new NoSuchEntityException(
                    __('Sub Account with provided id doesn\'t exist.')
                );
            }
            $id = $subaccount->getId();
            if (in_array($id, $subIdsArray)) {
                $this->subaccountTransportRepository->deleteById($id);
                $message = 'Success. Deleted succesfully Subaccount ID: %s';
                $result[] = [
                    'message' => sprintf($message, $subId),
                ];
            } else {
                throw new NoSuchEntityException(
                    __('Parent Account with provided id doesn\'t have sub accounts.')
                );
            }
        } else {
            throw new NoSuchEntityException(
                __('Customer with provided id is not Parent Account or has no related subaccounts.')
            );
        }
        return $result;
    }


    /**
     * Private method ensures that street value is an array.
     *
     * @param $address
     * @return array
     */
    private function getStreetArray($address)
    {
        $streetArray = [];

        foreach ($address->getStreet() as $street) {
            $streetArray[] = $street;
        }

        if (count($streetArray) == 0){
            $streetArray[] = '';
        }

        if (count($streetArray) == 1){
            $streetArray[] = '';
        }
        return $streetArray;
    }


    /**
     * Loads Sub Account Transport Data Object
     *
     * @param $sub
     * @return \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    private function getSubAccountTransportDataObject($sub)
    {
        $existingSubaccountTransportDataObject = $this
            ->subaccountTransportRepository
            ->getById($sub);
        $this->permission->loadSubaccountPermissions(
            $existingSubaccountTransportDataObject
        );

        return $existingSubaccountTransportDataObject;
    }
}