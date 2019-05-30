<?php

namespace Cminds\MultiUserAccounts\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class RegisterSuccess implements ObserverInterface
{
    private $_customerRepositoryInterface;

    private $scopeConfig;

    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //TODO observer or something else that will check if newly created customer can manage subaccounts or not
//        $customerId = $observer->getEvent()->getCustomer()->getId();
//        $customer = $this->_customerRepositoryInterface->getById($customerId);
//        $parentFlag = $this->scopeConfig->getValue('masteraccounts/create/confirm');
//        if ($parentFlag) {
//            $canManageSubaccount = 1;
//        } else {
//            $canManageSubaccount = 0;
//        }
//        $customer->setCustomAttribute('can_manage_subaccounts', $canManageSubaccount);
//        $this->_customerRepositoryInterface->save($customer);
//
//        return $this;
    }
}
