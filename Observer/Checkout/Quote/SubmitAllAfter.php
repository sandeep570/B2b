<?php

namespace Cminds\MultiUserAccounts\Observer\Checkout\Quote;

use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;

/**
 * Cminds MultiUserAccounts after order save observer.
 * Will be executed on "checkout_submit_all_after" event.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Rafal Andryanczyk <rafal.andryanczyk@gmail.com>
 */
class SubmitAllAfter implements ObserverInterface
{
    /**
     * Order sender object.
     *
     * @var OrderSender
     */
    private $orderSender;

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
     * Customer session object.
     *
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Customer factory object.
     *
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * SubmitAllAfter constructor.
     *
     * @param OrderSender     $orderSender Order sender object.
     * @param ModuleConfig    $moduleConfig Module config object.
     * @param ViewHelper      $viewHelper View helper object.
     * @param CustomerSession $customerSession Customer session object.
     * @param CustomerFactory $customerFactory Customer factory object.
     */
    public function __construct(
        OrderSender $orderSender,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        CustomerSession $customerSession,
        CustomerFactory $customerFactory
    ) {
        $this->orderSender = $orderSender;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Check permission to send order confirmation mail.
     *
     * @param Observer $observer Observer object.
     *
     * @return SubmitAllAfter
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();

        if ($this->viewHelper->isSubaccountLoggedIn() === false) {
            $this->orderSender->send($order);

            return $this;
        }

        $subaccountDataObject = $this->customerSession->getSubaccountData();

        $customerMaster = $this->customerFactory->create()
            ->load($subaccountDataObject->getParentCustomerId());
        $customerSubaccount = $this->customerFactory->create()
            ->load($subaccountDataObject->getCustomerId());

        $customerSubaccountName = $customerSubaccount->getFirstName()
            . ' '
            . $customerSubaccount->getLastName();

        $notificationConfig = $this->moduleConfig->getNotificationConfig();

        switch ($notificationConfig) {
            case ModuleConfig::NOTIFICATION_MAIN_ACCOUNT:
                if (!$this->moduleConfig->isSharedSessionEnabled()) {
                    $order
                        ->setCustomerName($customerSubaccountName)
                        ->setCustomerEmail($customerMaster->getEmail());
                    $this->orderSender->send($order);
                } else {
                    $this->orderSender->send($order);
                }
                break;
            case ModuleConfig::NOTIFICATION_SUBACCOUNT:
                if ($subaccountDataObject->getCheckoutOrderPlacedNotificationPermission()) {
                    $order
                        ->setCustomerName($customerSubaccountName)
                        ->setCustomerEmail($customerSubaccount->getEmail());
                    $this->orderSender->send($order);
                }
                break;
            case ModuleConfig::NOTIFICATION_BOTH:
                $order
                    ->setCustomerName($customerSubaccountName)
                    ->setCustomerEmail($customerMaster->getEmail());
                $this->orderSender->send($order);

                if ($subaccountDataObject->getCheckoutOrderPlacedNotificationPermission()) {
                    $order
                        ->setCustomerName($customerSubaccountName)
                        ->setCustomerEmail($customerSubaccount->getEmail());
                    $this->orderSender->send($order);
                }
                break;
        }

        $order->save();

        return $this;
    }
}
