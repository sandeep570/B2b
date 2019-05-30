<?php
namespace Cminds\MultiUserAccounts\Api\Data;

interface ApiSubAccountInterface
{
    const PARENT_EMAIL = 'parent_email';
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const GROUP_ID = 'group_id';
    const WEBSITE_ID = 'website_id';
    const PREFIX = 'prefix';
    const MIDDLENAME = 'middlename';
    const SUFFIX = 'suffix';
    const DOB = 'dob';
    const TAXVAT = 'taxvat';
    const GENDER = 'gender';
    const IS_ACTIVE = 'is_active';
    const SUPPLIER_APPROVE = 'supplier_approve';
    const COMPANY = 'company';
    const CITY = 'city';
    const COUNTRY_ID = 'country_id';
    const REGION = 'region';
    const POSTCODE = 'postcode';
    const TELEPHONE = 'telephone';
    const FAX = 'fax';
    const VAT_ID = 'vat_id';
    const STREET_1 = 'street_1';
    const STREET_2 = 'street_2';

    /**
     * Subaccount permission data keys.
     */
    const ACCOUNT_DATA_MODIFY_PERMISSION =
        'account_data_modification_permission';
    const ACCOUNT_ORDER_HISTORY_VIEW_PERMISSION =
        'account_order_history_view_permission';
    const CHECKOUT_ORDER_CREATE_PERMISSION =
        'checkout_order_create_permission';
    const CHECKOUT_ORDER_APPROVAL_PERMISSION =
        'checkout_order_approval_permission';
    const CHECKOUT_CART_VIEW_PERMISSION =
        'checkout_cart_view_permission';
    const CHECKOUT_VIEW_PERMISSION =
        'checkout_view_permission';
    const CHECKOUT_ORDER_PLACED_NOTIFICATION_PERMISSION =
        'checkout_order_placed_notification_permission';
    const FORCE_USAGE_PARENT_COMPANY_NAME_PERMISSION
        = 'force_usage_parent_company_name_permission';
    const FORCE_USAGE_PARENT_VAT_PERMISSION
        = 'force_usage_parent_vat_permission';
    const FORCE_USAGE_PARENT_ADDRESSES_PERMISSION
        = 'force_usage_parent_addresses_permission';
    const MANAGE_ORDER_APPROVAL_PERMISSION
        = 'manage_order_approval_permission';

    /**
     * @return mixed
     */
    public function setParentEmail($email);

    /**
     * @return mixed
     */
    public function getParentEmail();

    /**
     * @return mixed
     */
    public function setFirstname($firstname);

    /**
     * @return mixed
     */
    public function getFirstname();

    /**
     * @return mixed
     */
    public function setLastname($lastname);

    /**
     * @return mixed
     */
    public function getLastname();

    /**
     * @return mixed
     */
    public function setEmail($email);

    /**
     * @return mixed
     */
    public function getEmail();

    /**
     * @return mixed
     */
    public function setPassword($password);

    /**
     * @return mixed
     */
    public function getPassword();

    /**
     * @return mixed
     */
    public function setGroupId($groupId);

    /**
     * @return mixed
     */
    public function getGroupId();

    /**
     * @return mixed
     */
    public function setWebsiteId($websiteId);

    /**
     * @return mixed
     */
    public function getWebsiteId();

    /**
     * @return mixed
     */
    public function setPrefix($prefix);

    /**
     * @return mixed
     */
    public function getPrefix();

    /**
     * @return mixed
     */
    public function setMiddlename($middlename);

    /**
     * @return mixed
     */
    public function getMiddlename();

    /**
     * @return mixed
     */
    public function setSuffix($suffix);

    /**
     * @return mixed
     */
    public function getSuffix();

    /**
     * @return mixed
     */
    public function setDob($dob);

    /**
     * @return mixed
     */
    public function getDob();

    /**
     * @return mixed
     */
    public function setTaxvat($taxvat);

    /**
     * @return mixed
     */
    public function getTaxvat();

    /**
     * @return mixed
     */
    public function setGender($gender);

    /**
     * @return mixed
     */
    public function getGender();

    /**
     * @return mixed
     */
    public function setIsActive($isActive);

    /**
     * @return mixed
     */
    public function getIsActive();

    /**
     * @return mixed
     */
    public function setSupplierApprove($supplierApprove);

    /**
     * @return mixed
     */
    public function getSupplierApprove();

    /**
     * @return mixed
     */
    public function setCompany($company);

    /**
     * @return mixed
     */
    public function getCompany();

    /**
     * @return mixed
     */
    public function setCity($city);

    /**
     * @return mixed
     */
    public function getCity();

    /**
     * @return mixed
     */
    public function setCountryId($countryId);

    /**
     * @return mixed
     */
    public function getCountryId();

    /**
     * @return mixed
     */
    public function setRegion($region);

    /**
     * @return mixed
     */
    public function getRegion();

    /**
     * @return mixed
     */
    public function setPostcode($postcode);

    /**
     * @return mixed
     */
    public function getPostcode();

    /**
     * @return mixed
     */
    public function setTelephone($telephone);

    /**
     * @return mixed
     */
    public function getTelephone();

    /**
     * @return mixed
     */
    public function setFax($fax);

    /**
     * @return mixed
     */
    public function getFax();

    /**
     * @return $this
     */
    public function setVatId($vatId);

    /**
     * @return mixed
     */
    public function getVatId();

    /**
     * @return $this
     */
    public function setStreet1($street1);

    /**
     * @return mixed
     */
    public function getStreet1();

    /**
     * @return mixed
     */
    public function setStreet2($street2);

    /**
     * @return mixed
     */
    public function getStreet2();


    /**
     * Get subaccount permission.
     *
     * @return \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function getPermission();

    /**
     * Set subaccount permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setPermission($permission);

    /**
     * Get account data modification permission.
     *
     * @return int|null
     */
    public function getAccountDataModificationPermission();

    /**
     * Set account data modification permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setAccountDataModificationPermission($permission);

    /**
     * Get account order history view permission.
     *
     * @return int|null
     */
    public function getAccountOrderHistoryViewPermission();

    /**
     * Set account data modification permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setAccountOrderHistoryViewPermission($permission);

    /**
     * Get checkout order create permission.
     *
     * @return int|null
     */
    public function getCheckoutOrderCreatePermission();

    /**
     * Set checkout order create permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setCheckoutOrderCreatePermission($permission);

    /**
     * Get checkout order approval permission.
     *
     * @return int|null
     */
    public function getCheckoutOrderApprovalPermission();

    /**
     * Set checkout order approval permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setCheckoutOrderApprovalPermission($permission);

    /**
     * Get checkout cart view permission.
     *
     * @return int|null
     */
    public function getCheckoutCartViewPermission();

    /**
     * Set checkout cart view permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setCheckoutCartViewPermission($permission);

    /**
     * Get checkout view permission.
     *
     * @return int|null
     */
    public function getCheckoutViewPermission();

    /**
     * Set checkout view permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setCheckoutViewPermission($permission);

    /**
     * Get checkout order placed notification permission.
     *
     * @return int|null
     */
    public function getCheckoutOrderPlacedNotificationPermission();

    /**
     * Set checkout order placed notification permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setCheckoutOrderPlacedNotificationPermission($permission);

    /**
     * Get force usage parent company name permission.
     *
     * @return int|null
     */
    public function getForceUsageParentCompanyNamePermission();

    /**
     * Set force usage parent company name permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setForceUsageParentCompanyNamePermission($permission);

    /**
     * Get force usage parent vat permission.
     *
     * @return int|null
     */
    public function getForceUsageParentVatPermission();

    /**
     * Set force usage parent vat permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setForceUsageParentVatPermission($permission);

    /**
     * Get force usage parent addresses permission.
     *
     * @return int|null
     */
    public function getForceUsageParentAddressesPermission();

    /**
     * Set force usage parent addresses permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setForceUsageParentAddressesPermission($permission);

    /**
     * Set information if subaccount is promoted now
     *
     * @param   bool $promote
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setPromote($promote);

    /**
     * @return mixed
     */
    public function getPromote();

    /**
     * Set information if account is linked to parent
     *
     * @param   bool $parent
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setDowngrade($parent);

    /**
     * @return mixed
     */
    public function getDowngrade();
}