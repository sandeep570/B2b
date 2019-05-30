<?php

namespace Cminds\MultiUserAccounts\Controller\Adminhtml\Manage;

use Cminds\MultiUserAccounts\Controller\Adminhtml\AbstractManage;

/**
 * Cminds MultiUserAccounts adminhtml manage add controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Add extends AbstractManage
{
    /**
     * Subaccount add action.
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
