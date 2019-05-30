<?php

namespace Cminds\MultiUserAccounts\Model\Plugin\Customer\Authentication;

use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AuthenticationInterface;
use Cminds\MultiUserAccounts\Api\SubaccountRepositoryInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Cminds MultiUserAccounts authentication model plugin.
 *
 * @category Cminds
 * @package Cminds_MultiUserAccounts
 * @author Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    /**
     * Module config object.
     *
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Customer repository object.
     *
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Subaccount repository object.
     *
     * @var SubaccountRepositoryInterface
     */
    private $subaccountRepository;

    /**
     * Object initialization.
     *
     * @param ModuleConfig                  $moduleConfig Module config object.
     * @param CustomerRepositoryInterface   $customerRepository Customer repository object.
     * @param SubaccountRepositoryInterface $subaccountRepository Subaccount repository object.
     */
    public function __construct(
        ModuleConfig $moduleConfig,
        CustomerRepositoryInterface $customerRepository,
        SubaccountRepositoryInterface $subaccountRepository
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->customerRepository = $customerRepository;
        $this->subaccountRepository = $subaccountRepository;
    }

    /**
     * Around authentication plugin.
     *
     * @param AuthenticationInterface $subject
     * @param \Closure                $closure
     * @param int                     $customerId
     * @param string                  $password
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundAuthenticate(
        AuthenticationInterface $subject,
        \Closure $closure,
        $customerId,
        $password
    ) {
        try {
            $subaccountDataObject = $this->subaccountRepository
                ->getByCustomerId($customerId);
        } catch (NoSuchEntityException $e) {
            return $closure($customerId, $password);
        }

        $customerDataObject = $this->customerRepository->getById($customerId);
        $confirmation = $customerDataObject->getConfirmation();

        $isConfirmationRequired = $this->moduleConfig->isConfirmationRequired();
        if ($isConfirmationRequired === true && $confirmation) {
            throw new EmailNotConfirmedException(
                __('This account is not confirmed.')
            );
        }

        return $closure($customerId, $password);
    }
}
