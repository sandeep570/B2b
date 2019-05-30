<?php

namespace Cminds\MultiUserAccounts\Block\Adminhtml\Subaccount;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

/**
 * Cminds MultiUserAccounts admin subaccount edit block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Edit extends Container
{
    /**
     * Registry object.
     *
     * @var Registry
     */
    private $registry;

    /**
     * Object initialization.
     *
     * @param Context    $context Context object.
     * @param Registry   $registry Registry object.
     * @param array      $data Array data.
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data
    ) {
        $this->registry = $registry;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Initialize container.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_subaccount';
        $this->_blockGroup = 'Cminds_MultiUserAccounts';

        parent::_construct();
    }

    /**
     * Retrieve header text.
     *
     * @return string
     */
    public function getHeaderText()
    {
        $subaccountTransportDataObject = $this->registry->registry('subaccount');

        if ($subaccountTransportDataObject->getId()) {
            return __('Edit Subaccount');
        }

        return __('New Subaccount');
    }

    /**
     * Get URL for back (reset) button.
     *
     * @return string
     */
    public function getBackUrl()
    {
        $subaccountTransportDataObject = $this->registry->registry('subaccount');

        return $this->getUrl(
            'customer/index/edit',
            ['id' => $subaccountTransportDataObject->getParentCustomerId()]
        );
    }
}
