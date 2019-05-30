<?php

namespace Cminds\MultiUserAccounts\Observer\Adminhtml\Config;

use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Framework\App\Area as AppArea;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\Store;

/**
 * Cminds MultiUserAccounts after customer section configuration save observer.
 * Will be executed on "admin_system_config_changed_section_customer"
 * event in admin area.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class EmailConfirmation implements ObserverInterface
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
     * Scope config object.
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Storage writer object.
     *
     * @var WriterInterface
     */
    private $storageWriter;

    /**
     * SubmitAllAfter constructor.
     *
     * @param ModuleConfig         $moduleConfig Module config object.
     * @param AppState             $appState Application state object.
     * @param ScopeConfigInterface $scopeConfig Scope config object.
     * @param WriterInterface      $storageWriter Storage writer object.
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        AppState $appState,
        ScopeConfigInterface $scopeConfig,
        WriterInterface $storageWriter
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->appState = $appState;
        $this->scopeConfig = $scopeConfig;
        $this->storageWriter = $storageWriter;
    }

    /**
     * Check customer section configuration keys.
     * Make sure that if customer email confirmation is required then
     * subaccount email confirmation will be required as well.
     *
     * @param Observer $observer Observer object.
     *
     * @return EmailConfirmation
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        if ($this->appState->getAreaCode() !== AppArea::AREA_ADMINHTML) {
            return $this;
        }

        $customerEmailConfirmation = (bool)$this->scopeConfig->getValue(
            'customer/create_account/confirm'
        );

        $subaccountEmailConfirmation = $this->moduleConfig
            ->isConfirmationRequired();

        if ($customerEmailConfirmation === true
            && $subaccountEmailConfirmation === false
        ) {
            $this->storageWriter->save(
                'subaccount/new_subaccount/confirm',
                1,
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                Store::DEFAULT_STORE_ID
            );
        }

        return $this;
    }
}
