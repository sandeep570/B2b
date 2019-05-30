<?php

namespace Cminds\MultiUserAccounts\Block\Checkout\Address;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Registry as CoreRegistry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Cminds MultiUserAccounts checkout order approve button block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Mateusz Niziolek <mateusz@cminds.com>
 */
class AddressPermission extends Template
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Checkout session object.
     *
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * View helper object.
     *
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * Module config object.
     *
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Registry object.
     *
     * @var CoreRegistry
     */
    private $coreRegistry;

    /**
     * Object initialization.
     *
     * @param Context         $context Context object.
     * @param CheckoutSession $checkoutSession Checkout session object.
     * @param ViewHelper      $viewHelper View helper object.
     * @param ModuleConfig    $moduleConfig Module config object.
     * @param CoreRegistry    $coreRegistry Registry object.
     * @param array           $data Data array.
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        ViewHelper $viewHelper,
        ModuleConfig $moduleConfig,
        CoreRegistry $coreRegistry,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->viewHelper = $viewHelper;
        $this->moduleConfig = $moduleConfig;
        $this->coreRegistry = $coreRegistry;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Return order approval request url.
     *
     * @return string
     */
    public function getRequestOrderApprovalUrl()
    {
        return $this->getUrl('subaccounts/order_approve/request');
    }

    /**
     * Return bool value if button is disabled or not.
     *
     * @return bool
     */
    public function isDisabled()
    {
        return !$this->checkoutSession->getQuote()->validateMinimumAmount();
    }

    /**
     * Return bool value if button is visible or not.
     *
     * @return bool
     */
    public function isVisible()
    {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return false;
        }

        /**
         * Use saved previously subaccount data because for some reason
         * we're not able to get it from customer session.
         */
        /** @var SubaccountTransportInterface $subaccountDataObject */
        $subaccountTransportDataObject = $this->customerSession->getSubaccountData();
        $permission = (bool)$subaccountTransportDataObject
            ->getForceUsageParentAddressesPermission();

        if ($permission === true) {
            return false;
        }

        return true;
    }
}
