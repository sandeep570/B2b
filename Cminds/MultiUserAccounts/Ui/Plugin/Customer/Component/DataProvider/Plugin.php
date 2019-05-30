<?php

namespace Cminds\MultiUserAccounts\Ui\Plugin\Customer\Component\DataProvider;

use Cminds\MultiUserAccounts\Api\SubaccountRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Ui\Component\DataProvider;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Cminds MultiUserAccounts ui customer data provider component plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    /**
     * Subaccount repository object.
     *
     * @var SubaccountRepositoryInterface
     */
    private $subaccountRepository;

    /**
     * Customer repository object.
     *
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Object initialization.
     *
     * @param SubaccountRepositoryInterface $subaccountRepository Subaccount
     *     repository object.
     * @param CustomerRepositoryInterface   $customerRepository Customer
     *     repository object.
     */
    public function __construct(
        SubaccountRepositoryInterface $subaccountRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->subaccountRepository = $subaccountRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Add subaccount parent_customer_id id to data array.
     *
     * @param DataProvider $subject Subject object.
     * @param array        $data Data array.
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetData(
        DataProvider $subject,
        array $data
    ) {
        if (!isset($data['items'])) {
            return $data;
        }

        foreach ($data['items'] as &$item) {
            try {
                $subaccountModel = $this->subaccountRepository
                    ->getByCustomerId($item['entity_id']);

                $customerModel = $this->customerRepository
                    ->getById($subaccountModel->getParentCustomerId());

                $item['parent_customer_id'] = $subaccountModel
                    ->getParentCustomerId();
                $item['parent_customer_firstname'] = $customerModel
                    ->getFirstname();
                $item['parent_customer_lastname'] = $customerModel
                    ->getLastname();
            } catch (NoSuchEntityException $e) {
                $item['parent_customer_id'] = null;
                $item['parent_customer_firstname'] = null;
                $item['parent_customer_lastname'] = null;
            }
        }

        return $data;
    }
}
