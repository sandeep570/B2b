<?php

namespace Cminds\MultiUserAccounts\Controller\Plugin\Order;

use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\AuthenticationStateInterface;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;

/**
 * Cminds MultiUserAccounts order controller plugin.
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
     * @var AuthenticationStateInterface
     */
    private $authenticationState;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * @param CustomerSession              $customerSession
     * @param AuthenticationStateInterface $authenticationState
     * @param ModuleConfig                 $moduleConfig
     * @param ViewHelper                   $viewHelper
     */
    public function __construct(
        CustomerSession $customerSession,
        AuthenticationStateInterface $authenticationState,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper
    ) {
        $this->customerSession = $customerSession;
        $this->authenticationState = $authenticationState;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
    }

    /**
     * Perform customer authentication.
     *
     * @param ActionInterface  $subject
     * @param RequestInterface $request
     *
     * @return void
     * @throws NotFoundException
     */
    public function beforeDispatch(
        ActionInterface $subject,
        RequestInterface $request
    ) {
        if ($this->moduleConfig->isEnabled() === false) {
            throw new NotFoundException(__('Extension is disabled.'));
        }

        if ($this->viewHelper->isSubaccountLoggedIn()
            && $this->viewHelper->canManageOrderApprovals() === false
        ) {
            throw new NotFoundException(
                __('Subaccount does not have permission to view this page.')
            );
        }

        if ($this->authenticationState->isEnabled() &&
            !$this->customerSession->authenticate()
        ) {
            $subject->getActionFlag()->set('', 'no-dispatch', true);
        }
    }
}
