<?php

namespace Cminds\MultiUserAccounts\Observer\Checkout\Quote;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\Address;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\AddressFactory;
use Magento\Customer\Api\Data\AddressInterface;

/**
 * Cminds MultiUserAccounts quote submit before observer.
 * Will be executed on "checkout_submit_before" event.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class SubmitBefore implements ObserverInterface
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
     * Checkout session object.
     *
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * Address repository object.
     *
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var AddressFactory
     */
    private $quoteAddressFactory;

    /**
     * Object initialization.
     *
     * @param CustomerSession $customerSession Customer session object.
     * @param ModuleConfig    $moduleConfig Module config object.
     * @param ViewHelper      $viewHelper View helper object.
     * @param CheckoutSession $checkoutSession Checkout session object.
     * @param CustomerFactory $customerFactory Customer factory object.
     * @param AddressRepositoryInterface $addressRepository Address repository object.
     * @param AddressFactory $quoteAddressFactory
     */
    public function __construct(
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        CheckoutSession $checkoutSession,
        CustomerFactory $customerFactory,
        AddressRepositoryInterface $addressRepository,
        AddressFactory $quoteAddressFactory
    ) {
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->checkoutSession = $checkoutSession;
        $this->customerFactory = $customerFactory;
        $this->addressRepository = $addressRepository;
        $this->quoteAddressFactory = $quoteAddressFactory;
    }

    /**
     * Quote submit before event handler.
     *
     * @param Observer $observer Observer object.
     *
     * @return SubmitBefore
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return $this;
        }

        /** @var SubaccountTransportInterface $subaccountTransportDataObject */
        $subaccountTransportDataObject = $this->customerSession
            ->getSubaccountData();
        $quoteModel = $this->checkoutSession->getQuote();

        $checkoutOrderApprovalPermission = (bool)$subaccountTransportDataObject
            ->getCheckoutOrderApprovalPermission();

        $pass = false;
        if ($checkoutOrderApprovalPermission === true
            && (int)$quoteModel->getIsApproved() === 1
        ) {
            $pass = true;
        }

        $checkoutOrderCreatePermission = (bool)$subaccountTransportDataObject
            ->getCheckoutOrderCreatePermission();
        if ($pass === false && $checkoutOrderCreatePermission === true) {
            $pass = true;
        }

        if ($pass === false) {
            throw new LocalizedException(
                __('You don\'t have permission to create order.')
            );
        }

        $forceCompanyName = (bool)$subaccountTransportDataObject
            ->getForceUsageParentCompanyNamePermission();
        $forceVat = (bool)$subaccountTransportDataObject
            ->getForceUsageParentVatPermission();
        $forceAddresses = (bool)$subaccountTransportDataObject
            ->getForceUsageParentAddressesPermission();

        if ($forceCompanyName === false
            && $forceVat === false
            && $forceAddresses === false
        ) {
            return $this;
        }

        /** @var Customer $parentCustomer */
        $parentCustomer = $this->customerFactory->create()
            ->load($subaccountTransportDataObject->getParentCustomerId());

        /** @var Address $parentBillingAddress */
        $parentBillingAddress = $parentCustomer->getDefaultBillingAddress();

        /** @var Quote $quote */
        $quote = $observer->getQuote();

        if ($forceVat) {
            $quote->getCustomer()->setTaxvat($parentCustomer->getTaxvat());
        }

        if ($forceAddresses === false) {
            foreach ($quote->getAllAddresses() as $address) {
                if ($forceCompanyName) {
                    $address->setCompany($parentBillingAddress->getCompany());
                }
                if ($forceVat) {
                    $address->setVatId($parentBillingAddress->getVatId());
                }
            }
        } else {
            /** @var AddressInterface $parentBillingAddress */
            $parentBillingAddress = $this->addressRepository
                ->getById($parentBillingAddress->getId());
            $parentBillingAddress = $this->quoteAddressFactory->create()
                ->importCustomerAddressData($parentBillingAddress);

            $parentShippingAddress = $parentCustomer->getDefaultShippingAddress();
            $parentShippingAddress = $this->addressRepository
                ->getById($parentShippingAddress->getId());
            $parentShippingAddress = $this->quoteAddressFactory->create()
                ->importCustomerAddressData($parentShippingAddress);

            $quote->setBillingAddress($parentBillingAddress);
            $quote->setShippingAddress($parentShippingAddress);
        }

        return $this;
    }
}
