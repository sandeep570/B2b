<?php

namespace Cminds\MultiUserAccounts\Helper\Plugin\Customer\Session\CurrentCustomerAddress;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Helper\Session\CurrentCustomerAddress;
use Magento\Customer\Model\Session\Proxy as CustomerSession;

/**
 * Cminds MultiUserAccounts customer address helper plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    const PLUGIN_SKIP = 'cminds_multiuseraccounts_customer_address_collection_plugin_skip';

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * Object constructor.
     *
     * @param CustomerSession            $customerSession
     * @param ModuleConfig               $moduleConfig
     * @param ViewHelper                 $viewHelper
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        AccountManagementInterface $accountManagement
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->accountManagement = $accountManagement;
    }

    public function aroundGetDefaultBillingAddress(
        CurrentCustomerAddress $subject,
        \Closure $closure
    ) {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return $closure();
        }

        /** @var SubaccountTransportInterface $subaccountTransportDataObject */
        $subaccountTransportDataObject = $this->customerSession->getSubaccountData();

        $forceAddresses = (bool)$subaccountTransportDataObject
            ->getForceUsageParentAddressesPermission();

        if ($forceAddresses === false) {
            return $closure();
        }

        return $this->accountManagement
            ->getDefaultBillingAddress($subaccountTransportDataObject->getParentCustomerId());
    }

    public function aroundGetDefaultShippingAddress(
        CurrentCustomerAddress $subject,
        \Closure $closure
    ) {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return $closure();
        }

        /** @var SubaccountTransportInterface $subaccountTransportDataObject */
        $subaccountTransportDataObject = $this->customerSession->getSubaccountData();

        $forceAddresses = (bool)$subaccountTransportDataObject
            ->getForceUsageParentAddressesPermission();

        if ($forceAddresses === false) {
            return $closure();
        }

        return $this->accountManagement
            ->getDefaultShippingAddress($subaccountTransportDataObject->getParentCustomerId());
    }
}
