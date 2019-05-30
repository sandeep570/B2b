<?php

namespace Cminds\MultiUserAccounts\Controller\Adminhtml\Manage;

use Cminds\MultiUserAccounts\Controller\Adminhtml\AbstractManage;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Layout;
use Magento\Framework\View\Result\LayoutFactory;

/**
 * Cminds MultiUserAccounts adminhtml manage controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Index extends AbstractManage
{
    /**
     * Layout factory object.
     *
     * @var LayoutFactory
     */
    private $resultLayoutFactory;

    /**
     * Registry object.
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Object initialization.
     *
     * @param Context       $context Context object.
     * @param LayoutFactory $resultLayoutFactory Layout factory object.
     * @param Registry      $coreRegistry Registry object.
     */
    public function __construct(
        Context $context,
        LayoutFactory $resultLayoutFactory,
        Registry $coreRegistry
    ) {
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context);
    }

    /**
     * Subaccounts grid.
     *
     * @return Layout
     * @throws \RuntimeException
     */
    public function execute()
    {
        $this->initCurrentCustomer();
        
        return $this->resultLayoutFactory->create();
    }

    /**
     * Customer initialization.
     *
     * @return int
     * @throws \RuntimeException
     */
    private function initCurrentCustomer()
    {
        $customerId = (int)$this->getRequest()->getParam('id');

        if ($customerId) {
            $this->coreRegistry->register(
                RegistryConstants::CURRENT_CUSTOMER_ID,
                $customerId
            );
        }

        return $customerId;
    }
}
