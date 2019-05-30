<?php

namespace Cminds\MultiUserAccounts\Api;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;

/**
 * Cminds MultiUserAccounts subaccount transport interface.
 *
 * @category    Cminds
 * @package     Cminds_MultiUserAccounts
 * @author      Piotr Pierzak <piotr@cminds.com>
 */
interface SubaccountTransportRepositoryInterface
{
    /**
     * Create subaccount.
     *
     * @api
     * @param   \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface $subaccountTransportDataObject
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function save(
        SubaccountTransportInterface $subaccountTransportDataObject
    );

    /**
     * Retrieve subaccount.
     *
     * @api
     * @param   int $customerId
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function getByCustomerId($customerId);

    /**
     * Retrieve subaccount by subaccount id.
     *
     * @api
     * @param   int $subaccountId
     * @return  \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface
     */
    public function getById($subaccountId);

    /**
     * Delete subaccount.
     *
     * @api
     * @param   \Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface $subaccountTransportDataObject
     * @return  bool
     */
    public function delete(
        SubaccountTransportInterface $subaccountTransportDataObject
    );

    /**
     * Delete subaccount by id.
     *
     * @api
     * @param   int $subaccountId
     * @return  bool
     */
    public function deleteById($subaccountId);
}
