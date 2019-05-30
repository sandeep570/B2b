<?php

namespace Cminds\MultiUserAccounts\Model\Import;

use Cminds\MultiUserAccounts\Model\Permission;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Cminds\MultiUserAccounts\Api\Data\SubaccountInterface;

/**
 * Cminds MultiUserAccounts validator import model.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Validator
{
    /**
     * @var Permission
     */
    private $permission;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Object constructor.
     *
     * @param Permission $permission
     */
    public function __construct(
        Permission $permission,
        StoreManagerInterface $storeManager
    ) {
        $this->permission = $permission;
        $this->storeManager = $storeManager;
    }

    /**
     * Return field keys which are available in import.
     *
     * @return array
     */
    public function getKeys()
    {
        return array_merge_recursive(
            $this->getRequiredKeys(),
            $this->getCustomerProfileKeys(),
            $this->getCustomerAddressKeys(),
            $this->permission->getPermissionKeys()
        );
    }

    /**
     * Return field keys which are required in import.
     *
     * @return array
     */
    public function getRequiredKeys()
    {
        return [
            'parent_email',
            'firstname',
            'lastname',
            'email',
            'password',
        ];
    }

    /**
     * Check if all required keys exist.
     *
     * @param array $keys
     *
     * @return Validator
     * @throws LocalizedException
     */
    public function validateKeys(array $keys)
    {
        $diff = array_diff($this->getRequiredKeys(), $keys);
        if (!empty($diff)) {
            throw new LocalizedException(
                __('Required headers are missing: "%1".', implode(', ', $diff))
            );
        }

        return $this;
    }

    /**
     * Filter provided data, remove not allowed keys and return filtered array.
     *
     * @param array $data
     *
     * @return array
     */
    public function filterData(array $data)
    {
        $keys = $this->getKeys();
        foreach ($data as $key => $value) {
            if (in_array($key, $keys, true)) {
                continue;
            }

            unset($data[$key]);
        }

        return $data;
    }

    /**
     * Return default subaccount data array.
     *
     * @return array
     */
    public function getDefaultSubaccountData()
    {
        return [
            'customer_id' => '',
            'parent_customer_id' => '',
            'permission' => '',
            'promote' => '',
            'is_active' => SubaccountInterface::ACTIVE_FLAG,
        ];
    }

    /**
     * Return default customer profile data array.
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDefaultCustomerProfileData()
    {
        return [
            'website_id' => $this->storeManager->getWebsite()->getId(),
            'group_id' => $this->storeManager->getGroup()->getId(),
            'disable_auto_group_change' => '0',
            'prefix' => '',
            'firstname' => '',
            'middlename' => '',
            'lastname' => '',
            'suffix' => '',
            'email' => '',
            'dob' => '',
            'taxvat' => '',
            'gender' => '',
            'confirmation' => false,
            'sendemail' => false,
            'is_active' => '',
            'supplier_approve' => 0,
            'can_manage_subaccounts' => '' //TODO here is the place where we can set the default Can_Manage_Subaccounts
        ];
    }

    /**
     * Return customer profile keys.
     *
     * @return array
     */
    public function getCustomerProfileKeys()
    {
        $keys = array_keys($this->getDefaultCustomerProfileData());
        $keys = array_diff(
            $keys,
            [
                'disable_auto_group_change',
                'confirmation',
                'sendemail',
            ]
        );

        return $keys;
    }

    /**
     * Return default customer address data array.
     *
     * @return array
     */
    public function getDefaultCustomerAddressData()
    {
        return [
            'prefix' => '',
            'firstname' => '',
            'middlename' => '',
            'lastname' => '',
            'suffix' => '',
            'company' => '',
            'street' => [
                0 => '',
                1 => '',
            ],
            'city' => '',
            'country_id' => '',
            'region' => '',
            'postcode' => '',
            'telephone' => '',
            'fax' => '',
            'vat_id' => '',
            'default_billing' => true,
            'default_shipping' => true,
        ];
    }

    /**
     * Return customer profile keys.
     *
     * @return array
     */
    public function getCustomerAddressKeys()
    {
        $keys = array_keys($this->getDefaultCustomerAddressData());
        $keys = array_diff(
            $keys,
            [
                'default_billing',
                'default_shipping',
            ]
        );

        $keys[] = 'street_1';
        $keys[] = 'street_2';

        return $keys;
    }

    /**
     * @param array $addressData
     *
     * @return bool
     */
    public function validateCustomerAddressData(array $addressData)
    {
        $requiredValues = [
            'street',
            'city',
            'country_id',
            'postcode',
        ];

        foreach ($addressData as $key => $value) {
            if (in_array($key, $requiredValues, true) === false) {
                continue;
            }

            switch ($key) {
                case 'street':
                    if (empty($value[0])) {
                        return false;
                    }
                    break;
                default:
                    if (empty($value)) {
                        return false;
                    }
            }
        }

        return true;
    }
}
