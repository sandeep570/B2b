<?php

namespace Cminds\MultiUserAccounts\Observer\Sales\Service\Quote;

use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteFactory;

/**
 * Cminds MultiUserAccounts quote submit before observer.
 * Will be executed on "sales_model_service_quote_submit_before" event.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class SubmitBefore implements ObserverInterface
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
     * Quote factory object.
     *
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * Object initialization.
     *
     * @param CustomerSession $customerSession Customer session object.
     * @param ModuleConfig    $moduleConfig    Module config object.
     * @param ViewHelper      $viewHelper      View helper object.
     * @param QuoteFactory    $quoteFactory    Quote factory object.
     */
    public function __construct(
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        QuoteFactory $quoteFactory
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Quote submit before event handler.
     *
     * @param Observer $observer Observer object.
     *
     * @return SubmitBefore
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return $this;
        }

        $subaccountDataObject = $this->customerSession->getSubaccountData();
        $subaccountId = $subaccountDataObject->getId();

        $order = $observer->getOrder();
        $order->setSubaccountId($subaccountId);

        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create()->load($quoteId);

        $quote
            ->setSubaccountId($subaccountId)
            ->save();

        return $this;
    }
}
