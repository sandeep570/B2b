<?php

namespace Cminds\MultiUserAccounts\Controller\Plugin\Permission\Customer\Account\Confirm;

use Cminds\MultiUserAccounts\Api\SubaccountRepositoryInterface;
use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Cminds MultiUserAccounts customer account confirm controller plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
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
     * @var SubaccountRepositoryInterface
     */
    private $subaccountRepository;

    /**
     * @var SubaccountTransportRepositoryInterface
     */
    private $subaccountTransportRepository;

    /**
     * @var ManagerInterface
     */
    private $eventManager;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Object initialization.
     *
     * @param CustomerSession $customerSession
     * @param ModuleConfig $moduleConfig
     * @param ManagerInterface $eventManager
     * @param SubaccountRepositoryInterface $subaccountRepository
     * @param SubaccountTransportRepositoryInterface $subaccountTransportRepository
     * @param CustomerRepositoryInterface $customerRepository
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
     * Check if customer wants to login as subaccount.
     *
     * @param  ActionInterface $subject
     * @param  ResultInterface $result
     *
     * @return ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterExecute(
        ActionInterface $subject,
        ResultInterface $result
    ) {
        if ($this->moduleConfig->isEnabled() === false ||
            !$this->customerSession->isLoggedIn()
        ) {
            return $result;
        }

        /** @var CustomerInterface $customerDataObject */
        $customerDataObject = $this->customerSession->getCustomerData();

        try {
            $subaccountDataObject = $this->subaccountRepository
                ->getByCustomerId($customerDataObject->getId());
        } catch (NoSuchEntityException $e) {
            return $result;
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
            ->setCustomerDataAsLoggedIn($customerDataObject);
        $this->customerSession
            ->setSubaccountData($subaccountTransportDataObject);

        $this->eventManager->dispatch(
            'subaccount_data_object_login',
            [
                'customer' => $customerDataObject,
                'subaccount' => $subaccountDataObject,
            ]
        );

        return $result;
    }
}
