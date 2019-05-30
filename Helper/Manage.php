<?php

namespace Cminds\MultiUserAccounts\Helper;

use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Manage extends AbstractHelper
{
    /**
     * Session object.
     *
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Customer Repository Interface
     *
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Object initialization.
     *
     * @param Context         $context Context object.
     * @param CustomerSession $customerSession Session object.
     * @param ModuleConfig    $moduleConfig Module config object.
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;

        parent::__construct($context);
    }

    /**
     * Retrive customer attribute that controlls is master can manage its subs.
     *
     * @return boolean
     */
    public function getCanManageSubaccounts()
    {
        $result = false;
        if ($this->customerSession) {
            $customerId = $this->customerSession->getCustomer()->getId();
            $custObject = $this->customerRepository->getById($customerId);
            $canManageSubaccounts = $custObject->getCustomAttribute('can_manage_subaccounts');
            if (!is_null($canManageSubaccounts)) {
                $result = $canManageSubaccounts->getValue();
                return $result;
            }
        }
        return $result;
    }
}