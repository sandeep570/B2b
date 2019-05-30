<?php

namespace Cminds\MultiUserAccounts\Model\Plugin\Customer\AccountManagement;

use Cminds\MultiUserAccounts\Api\SubaccountRepositoryInterface;
use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Cminds MultiUserAccounts account management model plugin.
 *
 * @category Cminds
 * @package Cminds_MultiUserAccounts
 * @author Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
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
     * Subaccount repository object.
     *
     * @var SubaccountRepositoryInterface
     */
    private $subaccountRepository;

    /**
     * Subaccount transport repository object.
     *
     * @var SubaccountTransportRepositoryInterface
     */
    private $subaccountTransportRepository;

    /**
     * Event manager object.
     *
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * Customer repository object.
     *
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Object initialization.
     *
     * @param CustomerSession                        $customerSession
     * @param ModuleConfig                           $moduleConfig
     * @param ManagerInterface                       $eventManager
     * @param SubaccountRepositoryInterface          $subaccountRepository
     * @param SubaccountTransportRepositoryInterface $subaccountTransportRepository
     * @param CustomerRepositoryInterface            $customerRepository
     */
    public function __construct(
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig,
        ManagerInterface $eventManager,
        SubaccountRepositoryInterface $subaccountRepository,
        SubaccountTransportRepositoryInterface $subaccountTransportRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->eventManager = $eventManager;
        $this->subaccountRepository = $subaccountRepository;
        $this->subaccountTransportRepository = $subaccountTransportRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * After authentication plugin.
     *
     * @param AccountManagementInterface $accountManagement Account management object.
     * @param CustomerInterface          $customerDataObject Customer data object.
     *
     * @return CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterAuthenticate(
        AccountManagementInterface $accountManagement,
        CustomerInterface $customerDataObject
    ) {
        try {
            $subaccountDataObject = $this->subaccountRepository
                ->getByCustomerId($customerDataObject->getId());
        } catch (NoSuchEntityException $e) {
            return $customerDataObject;
        }

        if ($this->moduleConfig->isSharedSessionEnabled() === true) {
            $customerId = $subaccountDataObject->getParentCustomerId();
        } else {
            $customerId = $subaccountDataObject->getCustomerId();
        }

        /** @var CustomerInterface $customerDataObject */
        $customerDataObject = $this->customerRepository->getById($customerId);

        $subaccountTransportDataObject = $this->subaccountTransportRepository
            ->getById($subaccountDataObject->getId());
        $this->customerSession
            ->setSubaccountData($subaccountTransportDataObject);

        $this->eventManager->dispatch(
            'subaccount_data_object_login',
            [
                'customer' => $customerDataObject,
                'subaccount' => $subaccountDataObject,
            ]
        );

        return $customerDataObject;
    }
}
