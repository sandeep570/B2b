<?php

namespace Cminds\MultiUserAccounts\Observer\Adminhtml\Checkout\Quote;

use Cminds\MultiUserAccounts\Api\SubaccountRepositoryInterface;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Framework\App\Area as AppArea;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Cminds MultiUserAccounts after order save observer.
 * Will be executed on "checkout_submit_all_after" event in admin area.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class SubmitAllAfter implements ObserverInterface
{
    /**
     * Module config object.
     *
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Application state object.
     *
     * @var AppState
     */
    private $appState;

    /**
     * Subaccount repository object.
     *
     * @var SubaccountRepositoryInterface
     */
    private $subaccountRepository;

    /**
     * SubmitAllAfter constructor.
     *
     * @param ModuleConfig                  $moduleConfig
     * @param AppState                      $appState
     * @param SubaccountRepositoryInterface $subaccountRepository
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        AppState $appState,
        SubaccountRepositoryInterface $subaccountRepository
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->appState = $appState;
        $this->subaccountRepository = $subaccountRepository;
    }

    /**
     * Check permission to send order confirmation mail.
     *
     * @param Observer $observer Observer object.
     *
     * @return SubmitAllAfter
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return $this;
        }

        if ($this->appState->getAreaCode() !== AppArea::AREA_ADMINHTML) {
            return $this;
        }

        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $customerId = $order->getCustomerId();

        try {
            $subaccount = $this->subaccountRepository
                ->getByCustomerId($customerId);
        } catch (NoSuchEntityException $e) {
            return $this;
        }

        $order
            ->setSubaccountId($subaccount->getId())
            ->save();

        $quote
            ->setSubaccountId($subaccount->getId())
            ->save();

        return $this;
    }
}
