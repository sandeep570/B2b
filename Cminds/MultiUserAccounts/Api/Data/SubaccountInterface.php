<?php

namespace Cminds\MultiUserAccounts\Api\Data;

/**
 * Cminds MultiUserAccounts subaccount interface.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
interface SubaccountInterface
{
    /**
     * Subaccount entity data keys.
     */
    const ID = 'id';
    const CUSTOMER_ID = 'customer_id';
    const PARENT_CUSTOMER_ID = 'parent_customer_id';
    const PERMISSION = 'permission';
    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const ADDITIONAL_INFORMATION = 'additional_information';

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

    const MANAGE_ORDER_MAXIMUM_AMOUNT
        = 'manage_order_max_amount';

    /**
     * Subaccount is_active flags.
     */
    const ACTIVE_FLAG = 1;
    const NOT_ACTIVE_FLAG = 0;

    /**
     * Get subaccount id.
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set subaccount id.
     *
     * @param   int $id
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
     */
    public function setId($id);

    /**
     * Get subaccount customer id.
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set subaccount customer id.
     *
     * @param   int $id
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
     */
    public function setCustomerId($id);

    /**
     * Get subaccount parent customer id.
     *
     * @return int|null
     */
    public function getParentCustomerId();

    /**
     * Set subaccount parent customer id.
     *
     * @param   int $id
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
     */
    public function setParentCustomerId($id);

    /**
     * Get subaccount permission.
     *
     * @return int|null
     */
    public function getPermission();

    /**
     * Set subaccount permission.
     *
     * @param   int $permission
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
     */
    public function setPermission($permission);

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
     * @return  CustomerInterface
     */
    public function setIsActive($isActive);

    /**
     * Get subaccount created at date.
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set subaccount created at date.
     *
     * @param   string $date
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
     */
    public function setCreatedAt($date);

    /**
     * Get subaccount updated at date.
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set subaccount updated at date.
     *
     * @param   string $date
     *
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
     */
    public function setUpdatedAt($date);

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
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
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
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
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
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
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
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
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
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
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
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
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
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
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
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
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
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
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
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountInterface
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
}
