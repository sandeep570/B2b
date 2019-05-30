<?php

namespace Cminds\MultiUserAccounts\Controller\Manage;

use Cminds\MultiUserAccounts\Controller\AbstractManage;

/**
 * Cminds MultiUserAccounts manage add controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Add extends AbstractManage
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
