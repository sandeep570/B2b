<?php

namespace Cminds\MultiUserAccounts\Controller\Order\Approve;

use Cminds\MultiUserAccounts\Controller\AbstractOrder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Cminds MultiUserAccounts manage controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Waiting extends AbstractOrder
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Object initialization.
     *
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->getConfig()->getTitle()->set(
            __('Orders Waiting For Approval')
        );

        return $resultPage;
    }
}
