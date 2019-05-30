<?php

namespace Cminds\MultiUserAccounts\Observer\Customer\RegisterSuccess;

use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class ManageSubaccountsUpdate implements ObserverInterface
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * ManageSubaccountsUpdate constructor.
     *
     * @param ModuleConfig                $moduleConfig
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param Registry                    $coreRegistry
     * @param ViewHelper                  $viewHelper
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        CustomerRepositoryInterface $customerRepositoryInterface,
        Registry $coreRegistry,
        ViewHelper $viewHelper
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->customerRepository = $customerRepositoryInterface;
        $this->viewHelper = $viewHelper;
    }

    /**
     * @param Observer $observer
     *
     * @return ManageSubaccountsUpdate
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === true
        ) {
            return $this;
        }

        $canNewManage = (int)$this->moduleConfig->canNewCustomerManageSubaccounts();

        /** @var CustomerInterface $customer */
        $customer = $observer->getEvent()->getCustomer();
        $canManage = $customer->getCustomAttribute('can_manage_subaccounts');
        if ($canManage === null || (int)$canManage->getValue() !== $canNewManage) {
            $customer->setCustomAttribute(
                'can_manage_subaccounts',
                $canNewManage
            );
            $this->customerRepository->save($customer);
        }

        return $this;
    }
}
