<?php

namespace Cminds\MultiUserAccounts\Block\Adminhtml\Subaccount\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Cminds MultiUserAccounts admin subaccount edit form block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Form extends Generic
{
    /**
     * Prepare form method.
     *
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getData('action'),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
