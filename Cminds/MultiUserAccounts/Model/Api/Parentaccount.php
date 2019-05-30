<?php

namespace Cminds\MultiUserAccounts\Model\Api;

use Cminds\MultiUserAccounts\Api\Data\ApiParentAccountInterface;
use Cminds\MultiUserAccounts\Api\ParentaccountInterface;
use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Model\Import;
use Cminds\MultiUserAccounts\Model\Permission;
use Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount as SubaccountResourceModel;
use Cminds\MultiUserAccounts\Model\ResourceModel\SubaccountRepository as SubaccountRepository;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\DataObject;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Customer\Model\CustomerFactory;

class Parentaccount implements ParentaccountInterface
{

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var SubaccountTransportRepositoryInterface
     */
    private $subaccountTransportRepository;

    /**
     * @var Permission
     */
    private $permission;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Import
     */
    private $import;

    /**
     * @var SubaccountResourceModel
     */
    private $subaccountResourceModel;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var SubaccountRepository
     */
    private $subaccountRepository;

    public function __construct(
        AddressRepositoryInterface $addressRepository,
        CustomerRepositoryInterface $customerRepository,
        SubaccountResourceModel $subaccountResourceModel,
        Import $import,
        Registry $registry,
        Permission $permission,
        SubaccountTransportRepositoryInterface $subaccountTransportRepository,
        DataObjectFactory $dataObjectFactory,
        CustomerRegistry $customerRegistry,
        SubaccountRepository $subaccountRepository,
        CustomerFactory $customerFactory
    ) {
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
        $this->subaccountResourceModel = $subaccountResourceModel;
        $this->import = $import;
        $this->registry = $registry;
        $this->subaccountTransportRepository = $subaccountTransportRepository;
        $this->permission = $permission;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->customerRegistry = $customerRegistry;
        $this->subaccountRepository = $subaccountRepository;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Get the customer model loaded by its ID.
     *
     * @param int $id
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCustomerById($id)
    {
        return $this->customerRepository->getById($id);
    }

    /**
     * API endpoint that fetches parent account information.
     *
     *
     * @return DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($parentId)
    {
        try {
            $customer = $this->getCustomerById($parentId);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(
                __('Customer with provided id does not exists or is not a parent account.')
            );
        }

        try {
            $subArray = $this->subaccountResourceModel->getSubaccountIdsByParentCustomerId($customer->getId());
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(
                __('Customer with provided id does not exist')
            );
        }


        $customerData = [
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'email' => $customer->getEmail(),
            'is_active' => $customer->getCustomAttribute('customer_is_active')->getValue(),
            'can_manage_subaccounts' => $customer->getCustomAttribute('can_manage_subaccounts')->getValue(),
            'id' => $customer->getId(),
            'website_id' => $customer->getWebsiteId(),
            'group_id' => $customer->getGroupId(),
            'prefix' => $customer->getPrefix(),
            'middlename' => $customer->getMiddlename(),
            'suffix' => $customer->getSuffix(),
            'dob' => $customer->getDob(),
            'taxvat' => $customer->getTaxvat(),
            'gender' => $customer->getGender(),
            'company' => '',
            'city' => '',
            'country_id' => '',
            'region' => '',
            'postcode' => '',
            'telephone' => '',
            'fax' => '',
            'vat_id' => '',
            'street_1' => '',
            'street_2' => '',
        ];

        $addressId = $customer->getDefaultShipping();
        if ($addressId !== null) {
            $address = $this->addressRepository->getById($addressId);

            $streetArray = $this->getStreetArray($address);

            $customerData['city'] = $address->getCity();
            $customerData['country_id'] = $address->getCountryId();
            $customerData['region'] = $address->getRegionId();
            $customerData['postcode'] = $address->getPostcode();
            $customerData['telephone'] = $address->getTelephone();
            $customerData['fax'] = '';
            $customerData['vat_id'] = '';
            $customerData['street_1'] = $streetArray[0];
            $customerData['street_2'] = $streetArray[1];
        }

        $subaccountsData = [];
        $subaccountIds = $this->subaccountResourceModel
            ->getSubaccountIdsByParentCustomerId($parentId);

        foreach ($subaccountIds as $subaccountId) {
            $subaccountTransport = $this
                ->getSubAccountTransportDataObject($subaccountId);
            $subaccountCustomer = $this->getCustomerById($subaccountTransport->getCustomerId());

            if ($subaccountCustomer->getCustomAttribute('customer_is_active')) {
                $subActive = $subaccountCustomer->getCustomAttribute('customer_is_active')->getValue();
            } else {
                $subActive = 0;
            }

            $subaccountsData[$subaccountId] = [
                'id' => $subaccountCustomer->getId(),
                'parent_email' => $customer->getEmail(),
                'firstname' => $subaccountCustomer->getFirstname(),
                'lastname' => $subaccountCustomer->getLastname(),
                'email' => $subaccountCustomer->getEmail(),
                'website_id' => $subaccountCustomer->getWebsiteId(),
                'group_id' => $subaccountCustomer->getGroupId(),
                'prefix' => $subaccountCustomer->getPrefix(),
                'middlename' => $subaccountCustomer->getMiddlename(),
                'suffix' => $subaccountCustomer->getSuffix(),
                'dob' => $subaccountCustomer->getDob(),
                'taxvat' => $subaccountCustomer->getTaxvat(),
                'gender' => $subaccountCustomer->getGender(),
                'is_active' => $subActive,
                'company' => '',
                'city' => '',
                'country_id' => '',
                'region_id' => '',
                'postcode' => '',
                'telephone' => '',
                'fax' => '',
                'vat_id' => '',
                'street_1' => '',
                'street_2' => '',
                'account_data_modification_permission' => (int)$subaccountTransport
                    ->getAccountDataModificationPermission(),
                'account_order_history_view_permission' => (int)$subaccountTransport
                    ->getAccountOrderHistoryViewPermission(),
                'checkout_order_create_permission' => (int)$subaccountTransport
                    ->getCheckoutOrderCreatePermission(),
                'checkout_order_approval_permission' => (int)$subaccountTransport
                    ->getCheckoutOrderApprovalPermission(),
                'checkout_cart_view_permission' => (int)$subaccountTransport
                    ->getCheckoutCartViewPermission(),
                'checkout_view_permission' => (int)$subaccountTransport
                    ->getCheckoutViewPermission(),
                'checkout_order_placed_notification_permission' => (int)$subaccountTransport
                    ->getCheckoutOrderPlacedNotificationPermission(),
                'force_usage_parent_company_name_permission' => (int)$subaccountTransport
                    ->getForceUsageParentCompanyNamePermission(),
                'force_usage_parent_vat_permission' => (int)$subaccountTransport
                    ->getForceUsageParentVatPermission(),
                'force_usage_parent_addresses_permission' => (int)$subaccountTransport
                    ->getForceUsageParentAddressesPermission(),
            ];

            $subaccountAddress = null;
            foreach ($subaccountCustomer->getAddresses() as $address) {
                if ($address->isDefaultShipping()) {
                    $subaccountAddress = $address;
                }
            }

            if ($subaccountAddress !== null) {
                $streetSubArray = $this->getStreetArray($subaccountAddress);

                $subaccountsData[$subaccountId]['city'] = $subaccountAddress->getCity();
                $subaccountsData[$subaccountId]['country_id'] = $subaccountAddress->getCountryId();
                $subaccountsData[$subaccountId]['region_id'] = $subaccountAddress->getRegionId();
                $subaccountsData[$subaccountId]['postcode'] = $subaccountAddress->getPostcode();
                $subaccountsData[$subaccountId]['telephone'] = $subaccountAddress->getTelephone();
                $subaccountsData[$subaccountId]['street_1'] = $streetSubArray[0];
                $subaccountsData[$subaccountId]['street_2'] = $streetSubArray[1];
            }
        }

        $customerData['subaccounts'] = [];

        $result = $this->dataObjectFactory->create()
            ->setData($customerData);

        return $result;
    }

    public function create(array $parentCustomerData)
    {
        try {
            $this->import
                ->initSourceProcessor(Import::SOURCE_API)
                ->setApiData($parentCustomerData)
                ->setCanManageSub();
            $this->import->process();
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Parent account has been not created. Details: '.$e->getMessage().'.')
            );
        }

        $customer = $this->customerRegistry
            ->retrieveByEmail($parentCustomerData['email']);

        return $this->getById($customer->getId());
    }

    public function updateById($parentId, ApiParentAccountInterface $customer)
    {
        try {
            $customerEntity = $this->getCustomerById($parentId);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(
                __('Customer with provided id does not exist')
            );
        }

        try {
            $customerEntity->getCustomAttribute('can_manage_subaccounts');
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(
                    __('Customer with provided id is not a parent account.')
                );
        }

        try {
            $this->import->setUpdateFlag();
            $this->import->setCustomerId($parentId);
            $this->import
                ->initSourceProcessor(Import::SOURCE_API)
                ->setApiData($customer)
                ->setParentId($parentId);
            $this->import->process();
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Parent account has been not updated. Details: '.$e->getMessage().'.')
            );
        }

        return $this->getById($parentId);
    }

    public function deleteById($parentId)
    {
        try {
            $this->registry->register('isSecureArea', true);
            $customer = $this->customerRepository->getById($parentId);
        } catch (NoSuchEntityException $e) {
            throw new NoSuchEntityException(
                __('Customer with provided id doesn\'t exist.')
            );
        }
        try {
            $subAccount = $this->subaccountRepository->getById($parentId);
            if ($subAccount) {
                throw new LocalizedException(
                    __('Customer with provided id isn\'t parent account')
                );
            }
        } catch (NoSuchEntityException $e) {

        }
        if ($customer !== null) {
            $this->customerRepository->delete($customer);
        }
        return $result[] = [
            'message' => 'Success. Customer with provided id has been deleted.'
        ];
    }

    private function getStreetArray($address)
    {
        foreach ($address->getStreet() as $street) {
            $streetArray[] = $street;
        }
        if (count($streetArray) === 1) {
            $streetArray[] = '';
        }

        return $streetArray;
    }

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
