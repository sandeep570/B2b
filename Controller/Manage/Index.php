<?php

namespace Cminds\MultiUserAccounts\Controller\Manage;

use Cminds\MultiUserAccounts\Controller\AbstractManage;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Cminds MultiUserAccounts manage controller.
 *
 * @category    Cminds
 * @package     Cminds_MultiUserAccounts
 * @author      Piotr Pierzak <piotr@cminds.com>
 */
class Index extends AbstractManage
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param Context      $context
     * @param PageFactory  $resultPageFactory
     * @param ModuleConfig $moduleConfig
     * @param Session      $customerSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ModuleConfig $moduleConfig,
        Session $customerSession
    ) {
        parent::__construct($context);

        $this->resultPageFactory = $resultPageFactory;
        $this->moduleConfig = $moduleConfig;
        $this->customerSession = $customerSession;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Customer $customer */
        $customer = $this->customerSession->getCustomer();

        /** @var CustomerInterface $customer */
        $customer = $customer->getDataModel();

        $canManageSubaccounts = $customer->getCustomAttribute('can_manage_subaccounts');
        if ($canManageSubaccounts !== null) {
            $canManageSubaccounts = (int)$canManageSubaccounts->getValue();
        } else {
            $canManageSubaccounts = (int)$this->moduleConfig
                ->canParentAccountManageSubaccounts();
        }

        if ($canManageSubaccounts === 0) {
            $this->messageManager->addErrorMessage(
                __('Administrator disabled ability to create or edit subaccounts by you.')
            );
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->set(__('Manage Subaccounts'));

        return $resultPage;
    }
}
