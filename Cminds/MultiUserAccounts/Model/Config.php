<?php

namespace Cminds\MultiUserAccounts\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Cminds MultiUserAccounts config model.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Config
{
    const XML_PATH_SUBACCOUNT_GENERAL_ENABLE =
        'subaccount/general/enable';
    const XML_PATH_SUBACCOUNT_GENERAL_SHARED_SESSION =
        'subaccount/general/shared_session';
    const XML_PATH_SUBACCOUNT_GENERAL_NOTIFICATION =
        'subaccount/general/notification';
    const XML_PATH_SUBACCOUNT_CREATE_CONFIRM =
        'subaccount/new_subaccount/confirm';
    const XML_PATH_PARENTACCOUNT_GENERAL_CAN_MANAGE
        = 'parentaccount/general/can_manage';
    const XML_PATH_PARENTACCOUNT_NEW_CUSTOMER_CAN_MANAGE
        = 'parentaccount/new_customer/can_manage';
    const XML_PATH_PARENTACCOUNT_ORDER_APPROVAL_REQUEST_ALL_NOTIFICATION
        = 'parentaccount/order_approval_request/parentaccount_all_notification';
    const XML_PATH_SUBACCOUNT_ORDER_APPROVAL_REQUEST_AUTHORIZATION_REQUIRED
        = 'subaccount/order_approval_request/authorization_required';

    const NOTIFICATION_MAIN_ACCOUNT = 1;
    const NOTIFICATION_SUBACCOUNT = 2;
    const NOTIFICATION_BOTH = 3;

    /**
     * @var null|int
     */
    private $storeId = null;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Already fetched config values.
     *
     * @var array
     */
    private $config = [];

    /**
     * Object initialization.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Store id setter.
     *
     * @param   null|int $storeId
     *
     * @return  Config
     */
    public function setStoreId($storeId = null)
    {
        $this->storeId = $storeId;

        return $this;
    }

    /**
     * Return config field value.
     *
     * @param string $keyPath Key path.
     *
     * @return mixed
     */
    private function getConfigValue($keyPath)
    {
        if (isset($this->config[$keyPath]) === false) {
            $this->config[$keyPath] = $this->scopeConfig->getValue(
                $keyPath,
                ScopeInterface::SCOPE_STORE,
                $this->storeId
            );
        }

        return $this->config[$keyPath];
    }

    /**
     * Return bool value depends of that if module is enabled or not.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_SUBACCOUNT_GENERAL_ENABLE);
    }

    /**
     * Return bool value depends of that if shared session is enabled or not.
     *
     * @return bool
     */
    public function isSharedSessionEnabled()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_SUBACCOUNT_GENERAL_SHARED_SESSION);
    }

    /**
     * Return notification config value.
     *
     * @return int
     */
    public function getNotificationConfig()
    {
        return (int)$this->getConfigValue(self::XML_PATH_SUBACCOUNT_GENERAL_NOTIFICATION);
    }

    /**
     * Return bool value depends of that if confirmation
     * for new subaccounts is required ot not.
     *
     * @return bool
     */
    public function isConfirmationRequired()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_SUBACCOUNT_CREATE_CONFIRM);
    }

    /**
     * Return bool value depends of that if parent account can manage
     * subaccounts is enabled or not.
     *
     * @return bool
     */
    public function canParentAccountManageSubaccounts()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_PARENTACCOUNT_GENERAL_CAN_MANAGE);
    }

    /**
     * Return bool value depends of that if newly created customer can manage
     * subaccounts is enabled or not.
     *
     * @return bool
     */
    public function canNewCustomerManageSubaccounts()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_PARENTACCOUNT_NEW_CUSTOMER_CAN_MANAGE);
    }

    /**
     * Return bool value depends of that if parent account should receive all
     * order approval requests email notifications.
     *
     * @return bool
     */
    public function shouldParentReceiveAllNotifications()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_PARENTACCOUNT_ORDER_APPROVAL_REQUEST_ALL_NOTIFICATION);
    }

    /**
     * Return bool value depends of that if order approval requests
     * authorization is required.
     *
     * @return bool
     */
    public function isOrderApprovalRequestAuthorizationRequired()
    {
        return (bool)$this->getConfigValue(self::XML_PATH_SUBACCOUNT_ORDER_APPROVAL_REQUEST_AUTHORIZATION_REQUIRED);
    }
}
