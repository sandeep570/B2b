<?php

namespace Cminds\MultiUserAccounts\Controller\Manage;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterfaceFactory;
use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Controller\AbstractManage;
use Magento\Customer\Model\Session\Proxy as Session;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

/**
 * Cminds MultiUserAccounts manage edit controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Edit extends AbstractManage
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var SubaccountTransportRepositoryInterface
     */
    private $subaccountTransportRepository;

    /**
     * @var SubaccountTransportInterfaceFactory
     */
    private $subaccountTransportDataFactory;

    /**
     * Object initialization.
     *
     * @param Context                                $context
     * @param Session                                $session
     * @param PageFactory                            $resultPageFactory
     * @param DataObjectHelper                       $dataObjectHelper
     * @param SubaccountTransportRepositoryInterface $subaccountTransportRepository
     * @param SubaccountTransportInterfaceFactory    $subaccountTransportDataFactory
     */
    public function __construct(
        Context $context,
        Session $session,
        PageFactory $resultPageFactory,
        DataObjectHelper $dataObjectHelper,
        SubaccountTransportRepositoryInterface $subaccountTransportRepository,
        SubaccountTransportInterfaceFactory $subaccountTransportDataFactory
    ) {
        $this->session = $session;
        $this->resultPageFactory = $resultPageFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->subaccountTransportRepository = $subaccountTransportRepository;
        $this->subaccountTransportDataFactory = $subaccountTransportDataFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $subaccountId = $this->getRequest()->getParam('id');

        if ($subaccountId) {
            $resultPage->getConfig()->getTitle()->set(__('Edit Subaccount'));
        } else {
            $resultPage->getConfig()->getTitle()->set(__('Add Subaccount'));
        }
        $resultPage->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);

        try {
            $subaccountTransportDataObject = $this->subaccountTransportRepository->getById($subaccountId);

            if ((int)$subaccountTransportDataObject->getParentCustomerId() !== (int)$this->session->getCustomerId()) {
                throw new NoSuchEntityException(__('Subaccount with provided id does not exists.'));
            }
        } catch (NoSuchEntityException $e) {
            $subaccountTransportDataObject = $this->subaccountTransportDataFactory->create();
        }

        $subaccountFormData = $this->session->getSubaccountFormData();

        $this->dataObjectHelper->populateWithArray(
            $subaccountTransportDataObject,
            is_array($subaccountFormData) ? $subaccountFormData : [],
            '\Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface'
        );

        $this->session->setSubaccountFormData($subaccountTransportDataObject);

        return $resultPage;
    }
}
