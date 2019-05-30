<?php

namespace Cminds\MultiUserAccounts\Observer\Customer\Address;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Cminds MultiUserAccounts after customer address load observer.
 * Will be executed on "customer_address_load_after" event.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class LoadAfter implements ObserverInterface
{
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
     * Object constructor.
     *
     * @param CustomerSession $customerSession
     * @param ModuleConfig    $moduleConfig
     * @param ViewHelper      $viewHelper
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        CustomerFactory $customerFactory
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Update sub-account data.
     *
     * @param Observer $observer Observer object.
     *
     * @return LoadAfter
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return $this;
        }

        /** @var Address $address */
        $address = $observer->getCustomerAddress();

        /** @var SubaccountTransportInterface $subaccountTransportDataObject */
        $subaccountTransportDataObject = $this->customerSession->getSubaccountData();

        $forceCompanyName = (bool)$subaccountTransportDataObject
            ->getForceUsageParentCompanyNamePermission();
        $forceVat = (bool)$subaccountTransportDataObject
            ->getForceUsageParentVatPermission();

        if ($forceCompanyName === false && $forceVat === false) {
            return $this;
        }

        /** @var Customer $parentCustomer */
        $parentCustomer = $this->customerFactory->create()
            ->load($subaccountTransportDataObject->getParentCustomerId());

        /** @var Address $defaultBillingAddress */
        $defaultBillingAddress = $parentCustomer->getDefaultBillingAddress();
        if ($defaultBillingAddress === false) {
            return $this;
        }

        if ($forceCompanyName) {
            $address->setCompany($defaultBillingAddress->getCompany());
        }
        if ($forceVat) {
            $address->setVatId($defaultBillingAddress->getVatId());
        }

        return $this;
    }
}
