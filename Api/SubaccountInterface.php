<?php

namespace Cminds\MultiUserAccounts\Api;

use Cminds\MultiUserAccounts\Api\Data\ApiSubAccountInterface;

/**
 * Interface SubaccountInterface
 *
 * @package Cminds\MultiUserAccounts\Api
 */

interface SubaccountInterface
{
    /**
     * Returns list of all subaccounts for given parent.
     *
     * @param integer $parentId Parent id.
     *
     * @return \Cminds\MultiUserAccounts\Api\Data\ApiSubAccountInterface
     *
     * @api
     */
    public function getAllSubs($parentId);


    /**
     * Returns Subaccount Account info.
     *
     * @param integer $parentId Parent id.
     * @param integer $subAId Sub id.
     *
     * @return \Cminds\MultiUserAccounts\Api\Data\ApiSubAccountInterface
     *
     * @api
     */
    public function getById($parentId, $subAId);

    /**
     * Create new Sub Account
     *
     * @param integer $parentId Parent id.
     * @param string[] $customer
     *
     * @return string|\Cminds\MultiUserAccounts\Api\Data\ApiParentAccountInterface
     *
     * @api
     */
    public function create($parentId, array $customer);

    /**
     * Update existing sub account information by Customer ID
     *
     * @param integer $parentId Parent id.
     * @param \Cminds\MultiUserAccounts\Api\Data\ApiSubAccountInterface $customer
     * @param integer $subId Sub id.
     *
     * @return \Cminds\MultiUserAccounts\Api\Data\ApiParentAccountInterface
     *
     * @api
     */
    public function updateById($parentId, ApiSubAccountInterface $customer, $subId);

    /**
     * Delete Sub Account by customer ID
     *
     * @param integer $parentId Parent id.
     * @param integer $subId Sub id.
     *
     * @return string
     *
     * @api
     */
    public function deleteById($parentId, $subId);
}