<?php

namespace Cminds\MultiUserAccounts\Controller\Manage;

use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Controller\AbstractManage;
use Magento\Framework\App\Action\Context;

/**
 * Cminds MultiUserAccounts manage delete controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Delete extends AbstractManage
{
    /**
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
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($this->getRequest()->isPost() === false) {
            return $resultRedirect->setPath('*/*/index');
        }

        try {
            $subaccountId = (int)$this->getRequest()->getParam('id');
            if ($subaccountId === null) {
                return $resultRedirect->setPath('*/*/index');
            }

            $this->subaccountTransportRepository->deleteById($subaccountId);
            $this->messageManager->addSuccessMessage(__('Subaccount has been deleted.'));

            $url = $this->buildUrl('*/*/index', ['_secure' => true]);

            return $resultRedirect->setUrl($this->_redirect->success($url));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('During subaccount delete process error has occurred.')
            );
        }

        return $resultRedirect->setUrl($this->_redirect->error($this->buildUrl('*/*/index')));
    }

    /**
     * Return generated url to provided route.
     *
     * @param   string $route
     * @param   array  $params
     *
     * @return  string
     */
    private function buildUrl($route = '', $params = [])
    {
        return $this->_url->getUrl($route, $params);
    }
}
