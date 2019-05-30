<?php

namespace Cminds\MultiUserAccounts\Controller\Plugin\Permission\Newsletter\Manage;

use Cminds\MultiUserAccounts\Api\Data\SubaccountInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;

/**
 * Cminds MultiUserAccounts customer newsletter manage controller plugin.
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
     * Object initialization.
     *
     * @param   CustomerSession   $customerSession
     * @param   ManagerInterface  $messageManager
     * @param   ModuleConfig      $moduleConfig
     * @param   ViewHelper        $viewHelper
     * @param   ResponseInterface $response
     * @param   UrlInterface      $urlBuilder
     */
    public function __construct(
        CustomerSession $customerSession,
        ManagerInterface $messageManager,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        ResponseInterface $response,
        UrlInterface $urlBuilder
    ) {
        $this->customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->response = $response;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Check if subaccount have permission to view this page.
     *
     * @param ActionInterface  $subject
     * @param RequestInterface $request
     *
     * @return null
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

        /** @var SubaccountInterface $subaccountDataObject */
        $subaccountDataObject = $this->customerSession->getSubaccountData();

        $accountDataModificationPermission = (bool)$subaccountDataObject
            ->getAccountDataModificationPermission();
        if ($accountDataModificationPermission === false) {
            $subject->getActionFlag()->set('', 'no-dispatch', true);

            $this->messageManager->addErrorMessage(
                __('You don\'t have permission to edit account newsletter configuration.')
            );

            $url = $this->urlBuilder->getUrl('customer/account');
            $encodedUenc = base64_encode($url);
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
