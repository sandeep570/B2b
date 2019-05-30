<?php

namespace Cminds\MultiUserAccounts\Block\Adminhtml\Subaccount\Edit\Tab;

use Cminds\MultiUserAccounts\Model\Permission as PermissionModel;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Cminds MultiUserAccounts admin subaccount edit tab permission block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Permission extends Generic implements TabInterface
{
    /**
     * Permission object.
     *
     * @var PermissionModel
     */
    private $permission;

    /**
     * Object initialization.
     *
     * @param Context             $context          Context object.
     * @param Registry            $registry         Registry object.
     * @param FormFactory         $formFactory      Form factory object.
     * @param PermissionModel     $permission       Permission object.
     * @param array               $data             Array data.
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        PermissionModel $permission,
        array $data = []
    ) {
        $this->permission = $permission;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $data
        );
    }

    /**
     * Prepare form method.
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $subaccountTransportDataObject = $this->_coreRegistry
            ->registry('subaccount');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('subaccount_');
        $form->setFieldNameSuffix('subaccount');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Permission')]
        );

        $permissions = $this->permission->getPermissions();
        foreach ($permissions as $permissionCode => $permissionData) {
            $permission = $subaccountTransportDataObject
                ->{$this->permission->getPermissionGetter($permissionCode)}();
            $fieldset->addField(
                $this->permission->getPermissionId($permissionCode),
                'checkbox',
                [
                    'name' => $permissionCode,
                    'label' => $permissionData['description'],
                    'required' => false,
                    'value' => 1,
                    'checked' => $permission
                        ? 'checked'
                        : '',
                ]
            );
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Permission');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Permission');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
