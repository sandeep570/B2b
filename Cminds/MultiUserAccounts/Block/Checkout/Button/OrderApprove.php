<?php

namespace Cminds\MultiUserAccounts\Block\Checkout\Button;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Framework\Registry as CoreRegistry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Cminds MultiUserAccounts checkout order approve button block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class OrderApprove extends Template
{
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
        array $data = []
    ) {
        $this->checkoutSession = $checkoutSession;
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
        $subaccountTransportDataObject = $this->coreRegistry
            ->registry('subaccountData');
        $permission = (bool)$subaccountTransportDataObject
            ->getCheckoutOrderApprovalPermission();

        if ($permission === false) {
            return false;
        }

        $quoteModel = $this->checkoutSession->getQuote();

        $subAccountMaxAllowedOrderAmount = (float) $subaccountTransportDataObject
            ->getAdditionalInformationValue($subaccountTransportDataObject::ORDER_MAX_AMOUNT);

        if($subAccountMaxAllowedOrderAmount != null && $subAccountMaxAllowedOrderAmount != 0) {
            $grandTotal = (float) $quoteModel->getGrandTotal();

            if($grandTotal <= $subAccountMaxAllowedOrderAmount) {
                return false;
            }
        }


        if ((int)$quoteModel->getIsApproved() === 1) {
            return false;
        }

        return true;
    }
}
