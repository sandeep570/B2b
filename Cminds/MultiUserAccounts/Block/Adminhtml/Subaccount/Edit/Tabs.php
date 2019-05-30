<?php

namespace Cminds\MultiUserAccounts\Block\Adminhtml\Subaccount\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * Cminds MultiUserAccounts admin subaccount edit tabs block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Tabs extends WidgetTabs
{
    /**
     * Tabs initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('subaccount_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Subaccount'));
    }

    /**
     * Before to html method.
     *
     * @return WidgetTabs
     * @throws \Exception
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'subaccount_info',
            [
                'label' => __('Subaccount Information'),
                'title' => __('Subaccount Information'),
                'content' => $this->getLayout()
                    ->createBlock(
                        'Cminds\MultiUserAccounts\Block\Adminhtml\Subaccount\Edit\Tab\Info'
                    )
                    ->toHtml(),
                'active' => true,
            ]
        );
        $this->addTab(
            'subaccount_password',
            [
                'label' => __('Password'),
                'title' => __('Password'),
                'content' => $this->getLayout()
                    ->createBlock(
                        'Cminds\MultiUserAccounts\Block\Adminhtml\Subaccount\Edit\Tab\Password'
                    )
                    ->toHtml(),
                'active' => false,
            ]
        );
        $this->addTab(
            'subaccount_permission',
            [
                'label' => __('Permission'),
                'title' => __('Permission'),
                'content' => $this->getLayout()
                    ->createBlock(
                        'Cminds\MultiUserAccounts\Block\Adminhtml\Subaccount\Edit\Tab\Permission'
                    )
                    ->toHtml(),
                'active' => false,
            ]
        );
        $this->addTab(
            'subaccount_additional_configuration',
            [
                'label' => __('Additional Configuration'),
                'title' => __('Additional Configuration'),
                'content' => $this->getLayout()
                    ->createBlock(
                        'Cminds\MultiUserAccounts\Block\Adminhtml\Subaccount\Edit\Tab\AdditionalConfiguration'
                    )
                    ->toHtml(),
                'active' => false,
            ]
        );

        return parent::_beforeToHtml();
    }
}
