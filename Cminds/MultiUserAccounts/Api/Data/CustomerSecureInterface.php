<?php

namespace Cminds\MultiUserAccounts\Api\Data;

/**
 * Cminds MultiUserAccounts customer secure interface.
 *
 * @category    Cminds
 * @package     Cminds_MultiUserAccounts
 * @author      Piotr Pierzak <piotr@cminds.com>
 */
interface CustomerSecureInterface
{
    /**
     * Subaccount entity data keys.
     */
    const PASSWORD_HASH = 'password_hash';
    const RP_TOKEN = 'rp_token';
    const RP_TOKEN_CREATED_AT = 'rp_token_created_at';

    /**
     * Get password hash.
     *
     * @api
     * @return  string
     */
    public function getPasswordHash();

    /**
     * Set password hash.
     *
     * @api
     * @param   string $hash
     * @return  SubaccountTransportInterface
     */
    public function setPasswordHash($hash);

    /**
     * Get rp token.
     *
     * @api
     * @return  string
     */
    public function getRpToken();

    /**
     * Set rp token.
     *
     * @api
     * @param   string $token
     * @return  SubaccountTransportInterface
     */
    public function setRpToken($token);

    /**
     * Get rp token created at.
     *
     * @api
     * @return  string
     */
    public function getRpTokenCreatedAt();

    /**
     * Set rp token created at.
     *
     * @api
     * @param   string $date
     * @return  SubaccountTransportInterface
     */
    public function setRpTokenCreatedAt($date);
}
