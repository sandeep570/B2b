<?php

namespace Cminds\MultiUserAccounts\Block\Adminhtml\Customer\Edit\Tab\Subaccount\Grid\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Cminds MultiUserAccounts subaccounts grid name renderer block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Name extends AbstractRenderer
{
    /**
     * @param DataObject $row
     *
     * @return string
     */
    protected function _getValue(DataObject $row)
    {
        return trim(
            sprintf('%s %s', $row->getFirstname(), $row->getLastname())
        );
    }
}
