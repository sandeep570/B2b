<?php

namespace Cminds\MultiUserAccounts\Controller\Adminhtml\Manage;

use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Controller\Adminhtml\AbstractManage;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;

/**
 * Cminds MultiUserAccounts admin manage delete controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Delete extends AbstractManage
{
    /**
     * Subaccount transport repository object.
     *
     * @var SubaccountTransportRepositoryInterface
     */
    private $subaccountTransportRepository;

    /**
     * Object initialization.
     *
     * @param Context                                $context
     * @param SubaccountTransportRepositoryInterface $subaccountTransportRepository
     */
    public function __construct(
        Context $context,
        SubaccountTransportRepositoryInterface $subaccountTransportRepository
    ) {
        $this->subaccountTransportRepository = $subaccountTransportRepository;

        parent::__construct($context);
    }

    /**
     * Subaccount admin delete action.
     *
     * @return Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        $subaccountId = (int)$this->getRequest()->getParam('id');
        if ($subaccountId === null) {
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }

        try {
            $subaccountTransportDataObject = $this->subaccountTransportRepository
                ->getById($subaccountId);
            $parentCustomerId = $subaccountTransportDataObject
                ->getParentCustomerId();

            $this->subaccountTransportRepository->deleteById($subaccountId);
            $this->messageManager->addSuccess(__('Subaccount has been deleted.'));

            return $resultRedirect->setPath(
                'customer/index/edit',
                [
                    '_secure' => true,
                    'id' => $parentCustomerId,
                ]
            );
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('During subaccount delete process error has occurred.')
            );
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
