<?php

namespace Cminds\MultiUserAccounts\Model\Import\Source;

use Cminds\MultiUserAccounts\Model\Import\SourceInterface;
use Cminds\MultiUserAccounts\Model\Import\Validator;
use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Csv as CsvProcessor;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Setup\Exception;

/**
 * Cminds MultiUserAccounts API source import model.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Mateusz Niziołek <mateusz.niziolek@gmail.com>
 */
class Api implements SourceInterface
{
    /**
     * @var boolean
     */
    public $updateFlag;

    /**
     * @var Validator
     */
    private $validator;

    /**
     * @var array
     */
    private $apiData;

    /**
     * @var integer
     */
    private $parentId;

    /**
     * @var string
     */
    private $parentEmail;

    /**
     * @var integer
     */
    private $customerRepository;

    /**
     * @var integer
     */
    private $addressRepository;

    /**
     * @var integer
     */
    public $subId;

    /**
     * @var \Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
     */
    public $subaccountTransportRepository;

    /**
     * @var bool
     */
    public $canManageSub;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Object constructor.
     *
     * @param Validator $validator
     * @param CsvProcessor $csvProcessor
     */
    public function __construct(
        Validator $validator,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        SubaccountTransportRepositoryInterface $subaccountTransportRepository,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->validator = $validator;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->subaccountTransportRepository = $subaccountTransportRepository;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed[] Account Data Array
     */
    public function getAccountsData()
    {
        if ($this->updateFlag) {


            if ($this->parentId && $this->subId === null) {
                $customerId = $this->parentId;
                $customer = $this->customerRepository->getById($this->parentId);
                $addressId = $customer->getDefaultShipping();

                $customerData = $customer->__toArray();
                foreach ($customerData as $key => $item) {
                    $data[0][$key] = $item;
                }

                if ($addressId) {
                    $address = $this->addressRepository->getById($addressId);
                    $addressData = $address->__toArray();

                    foreach ($addressData as $key => $item) {
                        $data[0][$key] = $item;
                    }
                }
            }
            if ($this->subId) {
                $flag = false;
                try {
                    $subAccount = $this->subaccountTransportRepository->getByCustomerId($this->subId);
                    $subAccountData = $subAccount->__toArray();
                    foreach ($subAccountData as $key => $item) {
                        $data[0][$key] = $item;
                    }
                } catch (\Exception $e) {
                    $flag = true;
                }

                $customerId = $this->subId;
                $customer = $this->customerRepository->getById($customerId);
                $addressId = $customer->getDefaultShipping();

                if ($addressId) {
                    $address = $this->addressRepository->getById($addressId);
                    $addressData = $address->__toArray();

                    foreach ($addressData as $key => $item) {
                        $data[0][$key] = $item;
                    }
                }

                if ($flag) {
                    $customerId = $this->subId;
                    $customer = $this->customerRepository->getById($customerId);
                    $addressId = $customer->getDefaultShipping();

                    $customerData = $customer->__toArray();
                    foreach ($customerData as $key => $item) {
                        $data[0][$key] = $item;
                    }

                    if ($addressId) {
                        $address = $this->addressRepository->getById($addressId);
                        $addressData = $address->__toArray();

                        foreach ($addressData as $key => $item) {
                            $data[0][$key] = $item;
                        }
                        $permissions = [
                            "account_data_modification_permission" => 1,
                            "account_order_history_view_permission" => 1,
                            "checkout_order_create_permission" => 1,
                            "checkout_order_approval_permission" => 1,
                            "checkout_cart_view_permission" => 1,
                            "checkout_view_permission" => 1,
                            "checkout_order_placed_notification_permission" => 1,
                            "force_usage_parent_company_name_permission" => 1,
                            "force_usage_parent_vat_permission" => 1,
                            "force_usage_parent_addresses_permission" => 1,
                            "is_active" => 1
                        ];

                        foreach ($permissions as $key => $value) {
                            $data[0][$key] = $value;
                        }
                    }
                }
            }
        }

        if (!isset($data[0]['is_active'])) {
            $data[0]['is_active'] = 1;
            //TODO Check if user is active and return same value if is active not set in api call
        }

        $apiArray = $this->apiData;

        //TODO - refactor encapsulate this to selfdescriptive names of methods
        if (!is_array($apiArray)) {
            $apiArray = $apiArray->__toArray();
        }
        foreach ($apiArray as $key => $apiItem) {
            if ($key === 'street_1')
            {
                $data[0]['street'][0] = $apiItem;
            }
            if ($key === 'street_2')
            {
                $data[0]['street'][1] = $apiItem;
            }
            $data[0][$key] = $apiItem;
        }

        //
        if (!isset($this->subId) || $this->subId === null) {
            $addressKeys = [
                'street_1','street_2','city','region','postcode', 'country_id',
            ];
            foreach ($addressKeys as $key) {
                $data[0]['address'][$key] = $apiArray[$key] ;
            }
        }

        if (!isset($data[0]['parent_email'])) {
            if(isset($apiArray['parent_email'])) {
                $data[0]['parent_email'] = $apiArray['parent_email'];
            } else {
                $data[0]['parent_email'] = $this->parentEmail;
            }
        }

        if (isset($data[0]['can_manage_subaccounts']) && $data[0]['can_manage_subaccounts'] !== '') {
            // TODO check from ADMIN config if new parent is able to add new subaccounts
            $this->scopeConfig->getValue('parentaccount/general/can_manage');
        } else {
            $globalCanManageSubaccounts = $this->scopeConfig->getValue('parentaccount/general/can_manage');
            if (!$data[0]['parent_email']) {
                if ($globalCanManageSubaccounts) {
                    $data[0]['can_manage_subaccounts'] = $globalCanManageSubaccounts;
                }
            } else {
                if (isset($data[0]['promote'])) {
                    if ($data[0]['promote']) {
                        $data[0]['can_manage_subaccounts'] = 1;
                    } else {
                        $data[0]['can_manage_subaccounts'] = 0;
                    }
                }
            }
        }
        return $data;
    }

    /**
     * @param $apiData
     * @return $this
     */
    public function setApiData($apiData)
    {
        $this->apiData = $apiData;
        return $this;
    }

    /**
     * @param $parentId
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    /**
     * @return $this
     */
    public function setParentEmail()
    {
        $parentId = $this->parentId;
        $customer = $this->customerRepository->getById($this->parentId);
        $parentEmail = $customer->getEmail();
        $this->parentEmail = $parentEmail;
        return $this;
    }

    /**
     * @param $subId
     * @return $this
     */
    public function setSubId($subId)
    {
        $this->subId = $subId;
        return $this;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }
    // add Sub getter and setter later

    /**
     * @return string
     */
    public function getParentEmail()
    {
        return $this->parentEmail;
    }

    public function setCanManageSub()
    {
        $this->canManageSub = true;
        return $this;
    }

    public function setUpdateFlag($flag)
    {
        $this->updateFlag = $flag;
        return $this;
    }
}