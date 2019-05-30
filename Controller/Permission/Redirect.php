<?php

namespace Cminds\MultiUserAccounts\Controller\Permission;

use Magento\Framework\App\Action\Action as ActionController;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Cminds MultiUserAccounts permission redirect controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Redirect extends ActionController
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

        return $resultPage;
    }
}
