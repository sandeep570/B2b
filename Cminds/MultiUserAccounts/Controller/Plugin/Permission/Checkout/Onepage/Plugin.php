<?php

namespace Cminds\MultiUserAccounts\Controller\Plugin\Permission\Checkout\Onepage;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;

/**
 * Cminds MultiUserAccounts checkout onepage controller plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    /**
     * Customer session object.
     *
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * Message manager object.
     *
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * Module config object.
     *
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * View helper object.
     *
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * Response object.
     *
     * @var ResponseInterface
     */
    private $response;

    /**
     * Url builder object.
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Checkout session object.
     *
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * Object initialization.
     *
     * @param CustomerSession   $customerSession Customer session object.
     * @param ManagerInterface  $messageManager  Message manager object.
     * @param ModuleConfig      $moduleConfig    Module config object.
     * @param ViewHelper        $viewHelper      View helper object.
     * @param ResponseInterface $response        Response object.
     * @param UrlInterface      $urlBuilder      Url builder object.
     * @param CheckoutSession   $checkoutSession Checkout session object.
     */
    public function __construct(
        CustomerSession $customerSession,
        ManagerInterface $messageManager,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        ResponseInterface $response,
        UrlInterface $urlBuilder,
        CheckoutSession $checkoutSession
    ) {
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->response = $response;
        $this->urlBuilder = $urlBuilder;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Check if subaccount have permission to view this page.
     *
     * @param ActionInterface  $subject Subject object.
     * @param RequestInterface $request Request object.
     *
     * @return null
     */
    public function beforeDispatch(
        ActionInterface $subject,
        RequestInterface $request
    ) {
        if ($request->getPathInfo() === '/checkout/onepage/success/') {
            return null;
        }

        if ($this->moduleConfig->isEnabled() === false
            || $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return null;
        }

        $errorMessage = null;

        /** @var SubaccountTransportInterface $subaccountTransportDataObject */
        $subaccountTransportDataObject = $this->customerSession
            ->getSubaccountData();
        $quoteModel = $this->checkoutSession->getQuote();

        $checkoutOrderApprovalPermission = (bool)$subaccountTransportDataObject
            ->getCheckoutOrderApprovalPermission();

        $subAccountMaxAllowedOrderAmount = (float) $subaccountTransportDataObject
            ->getAdditionalInformationValue($subaccountTransportDataObject::ORDER_MAX_AMOUNT);

        $pass = false;

        if($subAccountMaxAllowedOrderAmount ==  null || $subAccountMaxAllowedOrderAmount == 0) {
            $pass = true;
        } else {
            $quote = $this->checkoutSession->getQuote();
            $grandTotal = (float) $quote->getGrandTotal();

            if($grandTotal <= $subAccountMaxAllowedOrderAmount) {
                $pass = true;
            }
        }

        if ($checkoutOrderApprovalPermission === true) {
            if ((int)$quoteModel->getIsApproved() === 1) {
                $pass = true;
            } else {
                if ($pass === false) {
                    $errorMessage = __(
                        'You can not go to the checkout page. '
                        . 'Please send order approval request first.'
                    );
                }
            }
        }

        $checkoutViewPermission = (bool)$subaccountTransportDataObject
            ->getCheckoutViewPermission();
        if ($pass === false
            && $checkoutViewPermission === true
            && $checkoutOrderApprovalPermission === false
        ) {
            $pass = true;
        }

        if ($pass === false) {
            $subject->getActionFlag()->set('', 'no-dispatch', true);

            if (empty($errorMessage)) {
                $errorMessage = __(
                    'You don\'t have permission to view the checkout page.'
                );
            }

            $this->messageManager->addErrorMessage($errorMessage);

            $rawUrl = '/';
            if ($checkoutViewPermission === true) {
                $rawUrl = 'checkout/cart';
            }

            $encodedUenc = base64_encode($this->urlBuilder->getUrl($rawUrl));
            $this->response->setRedirect(
                $this->urlBuilder->getUrl(
                    'subaccounts/permission/redirect',
                    ['uenc' => $encodedUenc]
                )
            );
        }

        return null;
    }
}
