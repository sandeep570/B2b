<?php

namespace Cminds\MultiUserAccounts\Controller\Plugin\Permission\Checkout\Cart\Index;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Registry as CoreRegistry;
use Magento\Framework\UrlInterface;

/**
 * Cminds MultiUserAccounts checkout cart index controller plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var CoreRegistry
     */
    private $coreRegistry;

    /**
     * Object initialization.
     *
     * @param CustomerSession   $customerSession
     * @param ManagerInterface  $messageManager
     * @param ModuleConfig      $moduleConfig
     * @param ViewHelper        $viewHelper
     * @param ResponseInterface $response
     * @param UrlInterface      $urlBuilder
     * @param CoreRegistry      $coreRegistry
     */
    public function __construct(
        CustomerSession $customerSession,
        ManagerInterface $messageManager,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        ResponseInterface $response,
        UrlInterface $urlBuilder,
        CoreRegistry $coreRegistry
    ) {
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->response = $response;
        $this->urlBuilder = $urlBuilder;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Check if subaccount have permission to view this page.
     *
     * @param ActionInterface  $subject
     * @param RequestInterface $request
     *
     * @return null
     * @throws \RuntimeException
     */
    public function beforeDispatch(
        ActionInterface $subject,
        RequestInterface $request
    ) {
        if ($this->moduleConfig->isEnabled() === false ||
            $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return null;
        }

        /** @var SubaccountTransportInterface $subaccountTransportDataObject */
        $subaccountTransportDataObject = $this->customerSession->getSubaccountData();

        $this->coreRegistry->register(
            'subaccountData',
            $subaccountTransportDataObject
        );

        $checkoutCartViewPermission = $subaccountTransportDataObject
            ->getCheckoutCartViewPermission();
        if ((bool)$checkoutCartViewPermission === false) {
            $subject->getActionFlag()->set('', 'no-dispatch', true);

            $this->messageManager->addErrorMessage(
                __('You don\'t have permission to view the cart page.')
            );

            $encodedUenc = base64_encode($this->urlBuilder->getUrl('/'));
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
