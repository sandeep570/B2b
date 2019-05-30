<?php

namespace Cminds\MultiUserAccounts\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Cminds MultiUserAccounts adminhtml abstract manage controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
abstract class AbstractManage extends Action
{
    /**
     * Subaccount access rights checking.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Cminds_MultiUserAccounts::manage_subaccounts'
        );
    }
}
