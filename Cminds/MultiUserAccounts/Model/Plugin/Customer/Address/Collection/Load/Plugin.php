<?php

namespace Cminds\MultiUserAccounts\Model\Plugin\Customer\Address\Collection\Load;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Address\Collection;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Registry;

/**
 * Cminds MultiUserAccounts customer address collection plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    const PLUGIN_SKIP = 'cminds_multiuseraccounts_customer_address_collection_plugin_skip';

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
     * @param Registry $coreRegistry
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

    public function afterLoad(
        Collection $subject,
        Collection $result
    ) {
        if ($this->coreRegistry->registry(self::PLUGIN_SKIP)) {
            return $result;
        }

        $this->coreRegistry->register(self::PLUGIN_SKIP, true);

        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            $this->coreRegistry->unregister(self::PLUGIN_SKIP);

            return $result;
        }

        /** @var SubaccountTransportInterface $subaccountTransportDataObject */
        $subaccountTransportDataObject = $this->customerSession->getSubaccountData();

        $forceCompanyName = (bool)$subaccountTransportDataObject
            ->getForceUsageParentCompanyNamePermission();
        $forceVat = (bool)$subaccountTransportDataObject
            ->getForceUsageParentVatPermission();

        if ($forceCompanyName === false && $forceVat === false) {
            $this->coreRegistry->unregister(self::PLUGIN_SKIP);

            return $this;
        }

        /** @var Customer $parentCustomer */
        $parentCustomer = $this->customerFactory->create()
            ->load($subaccountTransportDataObject->getParentCustomerId());

        /** @var Address $defaultBillingAddress */
        $defaultBillingAddress = $parentCustomer->getDefaultBillingAddress();

        if ($defaultBillingAddress) {
            foreach ($result as $address) {
                if ($forceCompanyName) {
                    $address->setCompany($defaultBillingAddress->getCompany());
                }
                if ($forceVat) {
                    $address->setVatId($defaultBillingAddress->getVatId());
                }
            }
        }

        $this->coreRegistry->unregister(self::PLUGIN_SKIP);

        return $result;
    }
}
