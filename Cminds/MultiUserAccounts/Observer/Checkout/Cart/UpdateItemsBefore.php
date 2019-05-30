<?php

namespace Cminds\MultiUserAccounts\Observer\Checkout\Cart;

use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Cminds MultiUserAccounts before cart items update observer.
 * Will be executed on "checkout_cart_update_items_before" event.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class UpdateItemsBefore implements ObserverInterface
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
     * Object initialization.
     *
     * @param ModuleConfig        $moduleConfig Module config object.
     * @param ViewHelper          $viewHelper View helper object.
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
    }

    /**
     * Check if quote has changed to reset approved flag.
     *
     * @param Observer $observer Observer object.
     *
     * @return UpdateItemsBefore
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return $this;
        }

        /** @var \Magento\Checkout\Model\Cart $cartModel */
        $cartModel = $observer->getCart();

        /** @var \Magento\Quote\Model\Quote $quoteModel */
        $quoteModel = $cartModel->getQuote();

        $quoteModel
            ->setIsApproved(0)
            ->setApproveHash(null);

        return $this;
    }
}
