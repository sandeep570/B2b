<?php

namespace Cminds\MultiUserAccounts\Model;

/**
 * Cminds MultiUserAccounts authentication state model interface.
 *
 * @category    Cminds
 * @package     Cminds_MultiUserAccounts
 * @author      Piotr Pierzak <piotr@cminds.com>
 */
interface AuthenticationStateInterface
{
    /**
     * Return bool value depends of that if we want
     * authentication enabled or not.
     *
     * @return bool
     */
    public function isEnabled();
}
