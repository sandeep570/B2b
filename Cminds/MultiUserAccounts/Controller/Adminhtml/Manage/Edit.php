<?php

namespace Cminds\MultiUserAccounts\Controller\Adminhtml\Manage;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterfaceFactory;
use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Controller\Adminhtml\AbstractManage;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Cminds MultiUserAccounts adminhtml manage edit controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Edit extends AbstractManage
{
    /**
     * Subaccount repository object.
     *
     * @var SubaccountTransportRepositoryInterface
     */
    private $subaccountTransportRepository;

    /**
     * Subaccount transport data factory object.
     *
     * @var SubaccountTransportInterfaceFactory
     */
    private $subaccountTransportDataFactory;

    /**
     * Page factory object.
     *
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * Registry object.
     *
     * @var Registry
     */
    private $registry;

    /**
     * Object initialization.
     *
     * @param Context                                $context Context object.
     * @param SubaccountTransportRepositoryInterface $subaccountTransportRepository Subaccount
     *     repository object.
     * @param SubaccountTransportInterfaceFactory    $subaccountTransportDataFactory Subaccount
     *     transport data factory object.
     * @param PageFactory                            $resultPageFactory Page
     *     factory object.
     * @param Registry                               $registry Registry object.
     */
    public function __construct(
        Context $context,
        SubaccountTransportRepositoryInterface $subaccountTransportRepository,
        SubaccountTransportInterfaceFactory $subaccountTransportDataFactory,
        PageFactory $resultPageFactory,
        Registry $registry
    ) {
        $this->subaccountTransportRepository = $subaccountTransportRepository;
        $this->subaccountTransportDataFactory = $subaccountTransportDataFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     * Subaccount admin edit action.
     *
     * @return Redirect
     * @throws \RuntimeException
     */
    public function execute()
    {
        $subaccountId = $this->getRequest()->getParam('id');

        $subaccountTransportDataObject = $this
            ->subaccountTransportDataFactory
            ->create();

        if ($subaccountId) {
            try {
                $subaccountTransportDataObject = $this
                    ->subaccountTransportRepository
                    ->getById($subaccountId);
            } catch (NoSuchEntityException $e) {
            }
        }

        $this->registry->register('subaccount', $subaccountTransportDataObject);

        $resultPage = $this->resultPageFactory->create();

        if ($subaccountId) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Subaccount'));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Subaccount'));
        }

        return $resultPage;
    }
}
