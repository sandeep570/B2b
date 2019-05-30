<?php

namespace Cminds\MultiUserAccounts\Controller\Order;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Cminds\MultiUserAccounts\Model\Service\Order\ApproveRequest;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Action\Action as ActionController;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Exception\NotFoundException;
use Magento\Quote\Model\QuoteFactory;

/**
 * Cminds MultiUserAccounts authorize controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Authorize extends ActionController
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var ApproveRequest
     */
    private $approveRequest;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * Object initialization.
     *
     * @param Context         $context
     * @param ModuleConfig    $moduleConfig
     * @param QuoteFactory    $quoteFactory
     * @param ApproveRequest  $approveRequest
     * @param CustomerSession $customerSession
     * @param ViewHelper      $viewHelper
     */
    public function __construct(
        Context $context,
        ModuleConfig $moduleConfig,
        QuoteFactory $quoteFactory,
        ApproveRequest $approveRequest,
        CustomerSession $customerSession,
        ViewHelper $viewHelper
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->quoteFactory = $quoteFactory;
        $this->approveRequest = $approveRequest;
        $this->customerSession = $customerSession;
        $this->viewHelper = $viewHelper;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(RequestInterface $request)
    {
        if ($this->moduleConfig->isEnabled() === false) {
            throw new NotFoundException(__('Extension is disabled.'));
        }

        if ($this->customerSession->isLoggedIn() === false) {
            $uenc = $this->getRequest()->getParam('uenc');
            $decodedUenc = base64_decode($uenc);

            $this->customerSession
                ->setAfterAuthUrl($decodedUenc)
                ->authenticate();
        }

        return parent::dispatch($request);
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $quoteId = $this->getRequest()->getParam('id');
        $hash = $this->getRequest()->getParam('hash');
        $uenc = $this->getRequest()->getParam('uenc');

        if (!empty($uenc)) {
            $decodedUenc = base64_decode($uenc);
            $failedRedirectUrl = $decodedUenc;
            $successRedirectUrl = $decodedUenc;
        } else {
            $failedRedirectUrl = '/';

            $encodedUenc = base64_encode($this->_url->getUrl('/'));
            $successRedirectUrl = $this->_url->getUrl(
                'subaccounts/permission/redirect',
                ['uenc' => $encodedUenc]
            );
        }

        if ($this->viewHelper->isSubaccountLoggedIn() === false) {
            $this->messageManager->addErrorMessage(
                __('Please login as subaccount with proper authorization permission.')
            );

            return $resultRedirect->setPath($failedRedirectUrl);
        }

        if (empty($quoteId) || empty($hash)) {
            return $resultRedirect->setPath($failedRedirectUrl);
        }

        /** @var \Magento\Quote\Model\Quote $quoteModel */
        $quoteModel = $this->quoteFactory->create()->load($quoteId);

        if (!$quoteModel->getId()) {
            return $resultRedirect->setPath($failedRedirectUrl);
        }

        $resultRedirect->setUrl($successRedirectUrl);

        if ((int)$quoteModel->getIsAuthorized() === 1) {
            $this->messageManager->addErrorMessage(
                __('Order is already authorized authorization.')
            );

            return $resultRedirect;
        }

        if ($quoteModel->getApproveHash() !== $hash) {
            $this->messageManager->addErrorMessage(
                __('Order authorization hash has expired or is incorrect.')
            );

            return $resultRedirect;
        }

        /** @var SubaccountTransportInterface $currentSubaccount */
        $currentSubaccount = $this->customerSession->getSubaccountData();

        $canAuthorize = $this->approveRequest
            ->canAuthorize($quoteModel, $currentSubaccount);

        if ($canAuthorize === false) {
            $this->messageManager->addErrorMessage(
                __('You are not allowed to authorize this order.')
            );

            return $resultRedirect;
        }

        try {
            $approvedLevel = $currentSubaccount
                ->getAdditionalInformationValue(SubaccountTransportInterface::MANAGE_ORDER_APPROVAL_PERMISSION_AMOUNT);

            $quoteModel
                ->setApproveHash(null)
                ->setAuthorizedRange($approvedLevel)
                ->save();

            $this->approveRequest->processNotification($quoteModel);

            $this->messageManager->addSuccessMessage(
                __('Order has been authorized.')
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('During order authorization process something goes wrong.')
            );
        }

        return $resultRedirect;
    }
}
