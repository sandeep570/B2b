<?php

namespace Cminds\MultiUserAccounts\Observer\Subaccount;

use Cminds\MultiUserAccounts\Api\Data\SubaccountInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Cminds MultiUserAccounts before customer save observer.
 * Will be executed on "subaccount_save_before" event.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class SaveBefore implements ObserverInterface
{
    /**
     * Customer session object.
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
     * View helper object.
     *
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * Customer repository object.
     *
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * Customer factory object.
     *
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * Data object helper object.
     *
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Data object processor object.
     *
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * Object constructor.
     *
     * @param CustomerSession     $customerSession     Customer session object.
     * @param ModuleConfig        $moduleConfig        Module config object.
     * @param ViewHelper          $viewHelper          View helper object.
     * @param CustomerRepository  $customerRepository  Customer repository object.
     * @param CustomerFactory     $customerFactory     Customer factory object.
     * @param DataObjectHelper    $dataObjectHelper    Data object helper object.
     * @param DataObjectProcessor $dataObjectProcessor Data object processor object.
     */
    public function __construct(
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        CustomerRepository $customerRepository,
        CustomerFactory $customerFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * Check permission in before save event handler.
     *
     * @param Observer $observer Observer object.
     *
     * @return SaveBefore
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws AuthenticationException
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === true
        ) {
            return $this;
        }

        /** @var \Cminds\MultiUserAccounts\Model\Subaccount $subaccountModel */
        $subaccountModel = $observer->getEvent()->getObject();

        /** @var SubaccountInterface $subaccountDataObject */
        $subaccountDataObject = $subaccountModel->getDataModel();

        $customerId = $subaccountModel->getCustomerId();
        if ($customerId) {
            /** @var CustomerInterface $customerDataObject */
            $customerDataObject = $this->customerRepository
                ->getById($customerId);
        } else {
            /** @var CustomerInterface $customerDataObject */
            $customerDataObject = $this->customerFactory->create()
                ->getDataModel();
        }

        $data = $this->dataObjectProcessor->buildOutputDataArray(
            $subaccountDataObject,
            '\Cminds\MultiUserAccounts\Api\Data\SubaccountInterface'
        );
        unset($data[$subaccountDataObject::ID]);

        $this->dataObjectHelper->populateWithArray(
            $customerDataObject,
            $data,
            '\Magento\Customer\Api\Data\CustomerInterface'
        );

        $customerDataObject = $this->customerRepository
            ->save($customerDataObject);
        if (!$customerId) {
            $subaccountModel->setCustomerId($customerDataObject->getId());
        }

        return $this;
    }
}
