<?php

namespace Cminds\MultiUserAccounts\Api;

use Cminds\MultiUserAccounts\Api\Data\SubaccountInterface;

/**
 * Cminds MultiUserAccounts subaccount crud interface.
 *
 * @category    Cminds
 * @package     Cminds_MultiUserAccounts
 * @author      Piotr Pierzak <piotr@cminds.com>
 */
interface SubaccountRepositoryInterface
{
    /**
     * Create subaccount.
     *
     * @api
     * @param   SubaccountInterface $subaccountDataObject
     * @return  SubaccountInterface
     */
    public function save(SubaccountInterface $subaccountDataObject);

    /**
     * Retrieve subaccount.
     *
     * @api
     * @param   int $customerId
     * @return  SubaccountInterface
     */
    public function getByCustomerId($customerId);

    /**
     * Retrieve subaccount.
     *
     * @api
     * @param   int $subaccountId
     * @return  SubaccountInterface
     */
    public function getById($subaccountId);

    /**
     * Delete subaccount.
     *
     * @api
     * @param   SubaccountInterface $subaccount
     * @return  bool
     */
    public function delete(SubaccountInterface $subaccount);

    /**
     * Delete subaccount by ID.
     *
     * @api
     * @param   int $subaccountId
     * @return  bool
     */
    public function deleteById($subaccountId);
}
