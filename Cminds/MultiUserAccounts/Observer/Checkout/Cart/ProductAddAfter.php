<?php

namespace Cminds\MultiUserAccounts\Observer\Checkout\Cart;

use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Cminds MultiUserAccounts after cart item add observer.
 * Will be executed on "checkout_cart_product_add_after" event.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class ProductAddAfter implements ObserverInterface
{
    /**
     * Module config object.
     *
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * View helper object.
     *
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * Checkout session object.
     *
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * Object initialization.
     *
     * @param ModuleConfig    $moduleConfig Module config object.
     * @param ViewHelper      $viewHelper View helper object.
     * @param CheckoutSession $checkoutSession Checkout session object.
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        CheckoutSession $checkoutSession
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Check if quote has changed to reset approved flag.
     *
     * @param Observer $observer Observer object.
     *
     * @return ProductAddAfter
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return $this;
        }

        /** @var \Magento\Quote\Model\Quote $quoteModel */
        $quoteModel = $this->checkoutSession->getQuote();

        $quoteModel
            ->setIsApproved(0)
            ->setApproveHash(null);

        return $this;
    }
}
