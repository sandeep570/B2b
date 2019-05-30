<?php

namespace Cminds\MultiUserAccounts\Helper;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

/**
 * Cminds MultiUserAccounts view helper.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class View extends AbstractHelper
{
    /**
     * Session object.
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
     * Object initialization.
     *
     * @param Context         $context Context object.
     * @param CustomerSession $customerSession Session object.
     * @param ModuleConfig    $moduleConfig Module config object.
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;

        parent::__construct($context);
    }

    /**
     * Concatenate all subaccount name parts into full subaccount name.
     *
     * @param SubaccountTransportInterface $subaccountTransportDataObject Subaccount
     *     transport data object.
     *
     * @return string
     */
    public function getSubaccountName(
        SubaccountTransportInterface $subaccountTransportDataObject
    ) {
        return trim(
            $subaccountTransportDataObject->getFirstname()
            . ' '
            . $subaccountTransportDataObject->getLastname()
        );
    }

    /**
     * Return bool value depends of that if subaccount is logged
     * in in current session.
     *
     * @return bool
     */
    public function isSubaccountLoggedIn()
    {
        /** @var SubaccountTransportInterface $subaccountDataObject */
        $subaccountTransportDataObject = $this->customerSession
            ->getSubaccountData();

        /** @var CustomerInterface $customerDataObject */
        $customerDataObject = $this->customerSession->getCustomerData();

        if ($subaccountTransportDataObject === null) {
            return false;
        }

        $parentCustomerId = $subaccountTransportDataObject
            ->getParentCustomerId();
        if ($this->moduleConfig->isSharedSessionEnabled() === true
            && (int)$parentCustomerId === (int)$customerDataObject->getId()
        ) {
            return true;
        }

        $customerId = $subaccountTransportDataObject->getCustomerId();
        if ($this->moduleConfig->isSharedSessionEnabled() === false
            && (int)$customerId === (int)$customerDataObject->getId()
        ) {
            return true;
        }

        return false;
    }

    /**
     * Return bool value depends of that if subaccount can manage orders
     * waiting for approval.
     *
     * @return bool
     */
    public function canManageOrderApprovals()
    {
        /** @var SubaccountTransportInterface $subaccountDataObject */
        $subaccountTransportDataObject = $this->customerSession
            ->getSubaccountData();

        if ($subaccountTransportDataObject === null) {
            return false;
        }

        $manageOrderApprovalPermission = (bool)$subaccountTransportDataObject
            ->getManageOrderApprovalPermission();

        return $manageOrderApprovalPermission === true;
    }
}
