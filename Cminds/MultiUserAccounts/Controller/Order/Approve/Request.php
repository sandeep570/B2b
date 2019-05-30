<?php

namespace Cminds\MultiUserAccounts\Controller\Order\Approve;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Cminds\MultiUserAccounts\Model\Service\Order\ApproveRequest as ApproveRequestService;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\Action\Action as ActionController;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Framework\Exception\NotFoundException;

/**
 * Cminds MultiUserAccounts abstract manage controller.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Request extends ActionController
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var ApproveRequestService
     */
    private $approveRequestService;

    /**
     * Object initialization.
     *
     * @param Context               $context
     * @param CheckoutSession       $checkoutSession
     * @param CustomerSession       $customerSession
     * @param ViewHelper            $viewHelper
     * @param ModuleConfig          $moduleConfig
     * @param ApproveRequestService $approveRequestService
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        ViewHelper $viewHelper,
        ModuleConfig $moduleConfig,
        ApproveRequestService $approveRequestService
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->customerSession = $customerSession;
        $this->viewHelper = $viewHelper;
        $this->moduleConfig = $moduleConfig;
        $this->approveRequestService = $approveRequestService;

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

        if ($this->viewHelper->isSubaccountLoggedIn() === false) {
            throw new NotFoundException(__(
                'Only subaccount have permission to view this page.'
            ));
        }

        /** @var SubaccountTransportInterface $subaccountTransportDataObject */
        $subaccountTransportDataObject = $this->customerSession
            ->getSubaccountData();

        $orderApprovalPermission = $subaccountTransportDataObject
            ->getCheckoutOrderApprovalPermission();
        if ((bool)$orderApprovalPermission === false) {
            throw new NotFoundException(__(
                'You don\'t have proper permission to view this page.'
            ));
        }

        return parent::dispatch($request);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        /** @var \Magento\Quote\Model\Quote $quoteModel */
        $quoteModel = $this->checkoutSession->getQuote();

        /** @var SubaccountTransportInterface $reqSubaccountTransportDataObject */
        $reqSubaccountTransportDataObject = $this->customerSession
            ->getSubaccountData();

        try {
            $quoteModel
                ->setSubaccountId($reqSubaccountTransportDataObject->getCustomerId())
                ->setIsApproved(0)
                ->setIsAuthorized(0)
                ->save();

            $this->approveRequestService->processNotification($quoteModel);

            $this->messageManager->addSuccessMessage(__(
                'Your order approval request has been sent.'
            ));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__(
                'During order approval request sending something goes wrong.'
            ));
        }
        $resultRedirect->setUrl($this->_url->getUrl('checkout/cart/index'));

        return $resultRedirect;
    }
}
