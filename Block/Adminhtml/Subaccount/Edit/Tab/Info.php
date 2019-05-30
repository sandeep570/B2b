<?php

namespace Cminds\MultiUserAccounts\Block\Adminhtml\Subaccount\Edit\Tab;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Model\Session\Proxy as Session;
use Magento\Customer\Helper\Address;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Registry;

/**
 * Cminds MultiUserAccounts admin subaccount edit tab info block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Info extends Generic implements TabInterface
{
    /**
     * Customer address object.
     *
     * @var Address
     */
    private $customerAddressHelper;

    /**
     * Customer factory object.
     *
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * Data processor object.
     *
     * @var DataObjectProcessor
     */
    private $dataProcessor;

    /**
     * Data object helper.
     *
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * Session object.
     *
     * @var Session
     */
    private $session;

    /**
     * Object initialization.
     *
     * @param Context             $context          Context object.
     * @param Registry            $registry         Registry object.
     * @param FormFactory         $formFactory      Form factory object.
     * @param DataObjectProcessor $dataProcessor    Data processor object.
     * @param Address             $address          Customer address object.
     * @param CustomerFactory     $customerFactory  Customer factory object.
     * @param DataObjectHelper    $dataObjectHelper Data object helper.
     * @param array               $data             Array data.
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        DataObjectProcessor $dataProcessor,
        Address $address,
        CustomerFactory $customerFactory,
        DataObjectHelper $dataObjectHelper,
        array $data = []
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->customerAddressHelper = $address;
        $this->customerFactory = $customerFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->session = $context->getBackendSession();

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $data
        );
    }

    /**
     * Retrieve subaccount transport object.
     *
     * @return SubaccountTransportInterface
     */
    private function getSubaccount()
    {
        $subaccountTransportDataObject = $this->_coreRegistry
            ->registry('subaccount');

        $subaccountFormData = $this->session->getSubaccountFormData(true);
        if ($subaccountFormData !== null) {
            $this->dataObjectHelper->populateWithArray(
                $subaccountTransportDataObject,
                $subaccountFormData,
                '\Cminds\MultiUserAccounts\Api\Data\SubaccountInterface'
            );
        }

        return $subaccountTransportDataObject;
    }

    /**
     * Prepare form method.
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $subaccountTransportDataObject = $this->getSubaccount();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('subaccount_');
        $form->setFieldNameSuffix('subaccount');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Subaccount Information')]
        );

        $parentCustomerId = $this->getRequest()->getParam('parent_customer_id');
        if ($parentCustomerId) {
            $fieldset->addField(
                'parent_customer_id',
                'hidden',
                ['name' => 'parent_customer_id']
            );
            $subaccountTransportDataObject
                ->setParentCustomerId($parentCustomerId);
        }

        if ($subaccountTransportDataObject->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }
        $fieldset->addField(
            'firstname',
            'text',
            [
                'name' => 'firstname',
                'label' => __('First Name'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'lastname',
            'text',
            [
                'name' => 'lastname',
                'label' => __('Last Name'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'required' => true,
            ]
        );

        $taxVatShowConfig = $this->customerAddressHelper->getConfig('taxvat_show');
        if (!empty($taxVatShowConfig)) {
            $taxVatRequired = false;
            if ($taxVatShowConfig === 'req') {
                $taxVatRequired = true;
            }
            if ($parentCustomerId) {
                $parentCustomer = $this->customerFactory->create()->load($parentCustomerId);
                if (!empty($parentCustomer->getTaxvat())) {
                    $subaccountTransportDataObject->setTaxvat($parentCustomer->getTaxvat());
                }
            }

            $fieldset->addField(
                'taxvat',
                'text',
                [
                    'name' => 'taxvat',
                    'label' => __('Tax/VAT Number'),
                    'required' => $taxVatRequired,
                ]
            );
        }

        $isActive = ($subaccountTransportDataObject->getId()
            && $subaccountTransportDataObject->getIsActive())
        || !$subaccountTransportDataObject->getId()
            ? true
            : false;

        $fieldset->addField(
            'is_active',
            'checkbox',
            [
                'name' => 'is_active',
                'label' => __('Is Active'),
                'required' => false,
                'value' => 1,
                'checked' => $isActive
                    ? 'checked'
                    : '',
            ]
        );
        $subaccountTransportDataObject->setIsActive(1);

        $subaccountTransportDataArray = $this->dataProcessor->buildOutputDataArray(
            $subaccountTransportDataObject,
            '\Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface'
        );

        $form->setValues($subaccountTransportDataArray);
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
        return __('Subaccount Information');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Subaccount Information');
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
