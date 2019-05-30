<?php

namespace Cminds\MultiUserAccounts\Block\Adminhtml\Customer\Edit\Tab\Subaccount\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Cminds MultiUserAccounts subaccounts grid status renderer block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Status extends AbstractRenderer
{
    /**
     * @param DataObject $row
     *
     * @return string
     */
    protected function _getValue(DataObject $row)
    {
        $value = parent::_getValue($row);

        return $value ? __('Active') : __('Inactive');
    }
}
