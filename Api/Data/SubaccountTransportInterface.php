<?php

namespace Cminds\MultiUserAccounts\Api\Data;

/**
 * Cminds MultiUserAccounts subaccount transport interface.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
interface SubaccountTransportInterface
{
    /**
     * Subaccount entity data keys.
     */
    const ID = 'id';
    const CUSTOMER_ID = 'customer_id';
    const PARENT_CUSTOMER_ID = 'parent_customer_id';
    const IS_ACTIVE = 'is_active';
    const CONFIRMATION = 'confirmation';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const ADDITIONAL_INFORMATION = 'additional_information';
    const EMAIL = 'email';
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const STORE_ID = 'store_id';
    const WEBSITE_ID = 'website_id';
    const PASSWORD_HASH = 'password_hash';
    const RP_TOKEN = 'rp_token';
    const RP_TOKEN_CREATED_AT = 'rp_token_created_at';
    const PERMISSION = 'permission';
    const TAXVAT = 'taxvat';
    const GROUP_ID = 'group_id';

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
        = 'force_usage_parent_company_name';
    const FORCE_USAGE_PARENT_VAT_PERMISSION
        = 'force_usage_parent_vat';
    const FORCE_USAGE_PARENT_ADDRESSES_PERMISSION
        = 'force_usage_parent_addresses';
    const MANAGE_ORDER_APPROVAL_PERMISSION
        = 'manage_order_approval_permission';

    /**
     * Subaccount additional information keys.
     */
    const MANAGE_ORDER_APPROVAL_PERMISSION_AMOUNT
        = 'manage_order_approval_permission_amount';
    const ORDER_MAX_AMOUNT
        = 'manage_order_max_amount';

    /**
     * Subaccount is_active flags.
     */
    const ACTIVE_FLAG = 1;
    const NOT_ACTIVE_FLAG = 0;

    /**
     * Get subaccount id.
     *
     * @api
     * @return  int|null
     */
    public function getId();

    /**
     * Set subaccount id.
     *
     * @api
     *
     * @param   int $id
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setId($id);

    /**
     * Get subaccount linked customer id.
     *
     * @api
     * @return  int
     */
    public function getCustomerId();

    /**
     * Set subaccount linked customer id.
     *
     * @api
     *
     * @param   int $customerId
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get subaccount parent customer id.
     *
     * @api
     * @return  int
     */
    public function getParentCustomerId();

    /**
     * Set subaccount parent customer id.
     *
     * @api
     *
     * @param   int $parentCustomerId
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setParentCustomerId($parentCustomerId);

    /**
     * Get is active.
     *
     * @api
     * @return  int
     */
    public function getIsActive();

    /**
     * Set is active.
     *
     * @api
     *
     * @param   int $isActive
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setIsActive($isActive);

    /**
     * Get confirmation.
     *
     * @api
     * @return string|null
     */
    public function getConfirmation();

    /**
     * Set confirmation.
     *
     * @api
     *
     * @param   string $confirmation
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setConfirmation($confirmation);

    /**
     * Get created at time.
     *
     * @api
     * @return  string|null
     */
    public function getCreatedAt();

    /**
     * Set created at time.
     *
     * @api
     *
     * @param   string $createdAt
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at time.
     *
     * @api
     * @return  string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated at time.
     *
     * @api
     *
     * @param   string $updatedAt
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get email address.
     *
     * @api
     * @return  string
     */
    public function getEmail();

    /**
     * Set email address.
     *
     * @api
     *
     * @param   string $email
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setEmail($email);

    /**
     * Get first name.
     *
     * @api
     * @return  string
     */
    public function getFirstname();

    /**
     * Set first name.
     *
     * @api
     *
     * @param   string $firstname
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setFirstname($firstname);

    /**
     * Get last name.
     *
     * @api
     * @return  string
     */
    public function getLastname();

    /**
     * Set last name.
     *
     * @api
     *
     * @param   string $lastname
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setLastname($lastname);

    /**
     * Get store id.
     *
     * @api
     * @return  int|null
     */
    public function getStoreId();

    /**
     * Set store id.
     *
     * @api
     *
     * @param   int $storeId
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setStoreId($storeId);

    /**
     * Get website id.
     *
     * @api
     * @return  int|null
     */
    public function getWebsiteId();

    /**
     * Set website id.
     *
     * @api
     *
     * @param   int $websiteId
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setWebsiteId($websiteId);

    /**
     * Get password hash.
     *
     * @api
     * @return  string
     */
    public function getPasswordHash();

    /**
     * Set password hash.
     *
     * @api
     *
     * @param   string $hash
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setPasswordHash($hash);

    /**
     * Get rp token.
     *
     * @api
     * @return  string
     */
    public function getRpToken();

    /**
     * Set rp token.
     *
     * @api
     *
     * @param   string $token
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setRpToken($token);

    /**
     * Get rp token created at.
     *
     * @api
     * @return  string
     */
    public function getRpTokenCreatedAt();

    /**
     * Set rp token created at.
     *
     * @api
     *
     * @param   string $date
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setRpTokenCreatedAt($date);

    /**
     * Get subaccount permission.
     *
     * @api
     * @return int|null
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
     * Get manage order approval permission.
     *
     * @return int|null
     */
    public function getManageOrderApprovalPermission();

    /**
     * Set manage order approval permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
     */
    public function setManageOrderApprovalPermission($permission);

    /**
     * Get additional information.
     *
     * @return string
     */
    public function getAdditionalInformation();

    /**
     * Set additional information.
     *
     * @param string $additionalInformation
     *
     * @return \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
     */
    public function setAdditionalInformation($additionalInformation);

    /**
     * Get taxvat.
     *
     * @return int|null
     */
    public function getTaxvat();

    /**
     * Set taxvat.
     *
     * @param   string $taxvat
     *
     * @return \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setTaxvat($taxvat);

    /**
     * Get group id.
     *
     * @return int|null
     */
    public function getGroupId();

    /**
     * Set group id.
     *
     * @param int $groupId
     *
     * @return \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setGroupId($groupId);

    /**
     * @param $data
     * @return \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function setPromote($data);

    /**
     * @return bool
     */
    public function getPromote();
}
