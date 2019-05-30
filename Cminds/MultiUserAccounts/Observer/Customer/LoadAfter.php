<?php

namespace Cminds\MultiUserAccounts\Observer\Customer;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

/**
 * Cminds MultiUserAccounts after customer load observer.
 * Will be executed on "customer_load_after" event.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class LoadAfter implements ObserverInterface
{
    const LOAD_EVENT_SKIP = 'cminds_multiuseraccounts_customer_load_event_skip';

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Object constructor.
     *
     * @param CustomerSession $customerSession
     * @param ModuleConfig    $moduleConfig
     * @param ViewHelper      $viewHelper
     * @param CustomerFactory $customerFactory
     * @param Registry        $coreRegistry
     */
    public function __construct(
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        CustomerFactory $customerFactory,
        Registry $coreRegistry
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->customerFactory = $customerFactory;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Update sub-account data.
     *
     * @param Observer $observer Observer object.
     *
     * @return LoadAfter
     * @throws \RuntimeException
     */
    public function execute(Observer $observer)
    {
        if ($this->coreRegistry->registry(self::LOAD_EVENT_SKIP)) {
            return $this;
        }

        $this->coreRegistry->register(self::LOAD_EVENT_SKIP, true);

        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            $this->coreRegistry->unregister(self::LOAD_EVENT_SKIP);

            return $this;
        }

        /** @var Customer $address */
        $customer = $observer->getCustomer();

        /** @var SubaccountTransportInterface $subaccountTransportDataObject */
        $subaccountTransportDataObject = $this->customerSession->getSubaccountData();

        $forceVat = (bool)$subaccountTransportDataObject
            ->getForceUsageParentVatPermission();

        if ($forceVat === false) {
            return $this;
        }

        /** @var Customer $parentCustomer */
        $parentCustomer = $this->customerFactory->create()
            ->load($subaccountTransportDataObject->getParentCustomerId());

        $this->coreRegistry->unregister(self::LOAD_EVENT_SKIP);

        if ($forceVat) {
            $customer->setTaxvat($parentCustomer->getTaxvat());
        }

        return $this;
    }
}
