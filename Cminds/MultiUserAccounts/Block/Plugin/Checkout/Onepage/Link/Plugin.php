<?php

namespace Cminds\MultiUserAccounts\Block\Plugin\Checkout\Onepage\Link;

use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Cminds MultiUserAccounts proceed to checkout button plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    /**
     * Module config object.
     *
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Checkout session object.
     *
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * Customer session object.
     *
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * View helper object.
     *
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * Object initialization.
     *
     * @param ModuleConfig    $moduleConfig Module config object.
     * @param CheckoutSession $checkoutSession Checkout session object.
     * @param CustomerSession $customerSession Customer session object.
     * @param ViewHelper      $viewHelper View helper object.
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        ViewHelper $viewHelper
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->viewHelper = $viewHelper;
    }

    /**
     * Around isPossibleOnepageCheckout plugin.
     *
     * @param BlockInterface $subject Subject object.
     * @param \Closure       $proceed Closure object.
     *
     * @return mixed
     */
    public function aroundIsPossibleOnepageCheckout(
        BlockInterface $subject,
        \Closure $proceed
    ) {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return $proceed();
        }

        $subaccountTransportDataObject = $this->customerSession
            ->getSubaccountData();

        $orderPermission = (bool)$subaccountTransportDataObject
            ->getCheckoutOrderCreatePermission();
        if ($orderPermission === true) {
            return $proceed();
        }

        $quoteModel = $this->checkoutSession->getQuote();

        $orderApprovalPermission = (bool)$subaccountTransportDataObject
            ->getCheckoutOrderApprovalPermission();
        if ($orderApprovalPermission === true
            && (int)$quoteModel->getIsApproved() === 0
        ) {
            return false;
        }

        return $proceed();
    }
}
