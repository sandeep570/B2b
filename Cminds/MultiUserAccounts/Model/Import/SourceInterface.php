<?php

namespace Cminds\MultiUserAccounts\Model\Import;

/**
 * Cminds MultiUserAccounts import source interface.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
interface SourceInterface
{
    /**
     * @return array
     */
    public function getAccountsData();
}
