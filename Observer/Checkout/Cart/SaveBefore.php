<?php

namespace Cminds\MultiUserAccounts\Observer\Checkout\Cart;

use Cminds\MultiUserAccounts\Api\Data\SubaccountInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Cminds MultiUserAccounts before cart save observer.
 * Will be executed on "checkout_cart_save_before" event.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class SaveBefore implements ObserverInterface
{
    /**
     * Customer session object.
     *
     * @var CustomerSession
     */
    private $customerSession;

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
     * @param CustomerSession $customerSession Customer session object.
     * @param ModuleConfig    $moduleConfig Module config object.
     * @param ViewHelper      $viewHelper View helper object.
     */
    public function __construct(
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
    }

    /**
     * Check permission in before save event handler.
     *
     * @param Observer $observer Observer object.
     *
     * @return SaveBefore
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return $this;
        }

        /** @var SubaccountInterface $subaccountDataObject */
        $subaccountDataObject = $this->customerSession->getSubaccountData();

        $checkoutCartViewPermission = (bool)$subaccountDataObject
            ->getCheckoutCartViewPermission();
        if ($checkoutCartViewPermission === false) {
            throw new LocalizedException(
                __('You don\'t have permission to do any modifications in the cart.')
            );
        }

        return $this;
    }
}
