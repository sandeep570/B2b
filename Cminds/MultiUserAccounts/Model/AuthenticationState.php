<?php

namespace Cminds\MultiUserAccounts\Model;

/**
 * Cminds MultiUserAccounts authentication state model.
 *
 * @category    Cminds
 * @package     Cminds_MultiUserAccounts
 * @author      Piotr Pierzak <piotr@cminds.com>
 */
class AuthenticationState implements AuthenticationStateInterface
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return true;
    }
}
