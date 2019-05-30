<?php

namespace Cminds\MultiUserAccounts\Controller\Plugin\Manage;

use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\AuthenticationStateInterface;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\UrlInterface;

/**
 * Cminds MultiUserAccounts manage controller plugin.
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
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Plugin constructor.
     *
     * @param Session\Proxy                $customerSession
     * @param AuthenticationStateInterface $authenticationState
     * @param ModuleConfig                 $moduleConfig
     * @param ViewHelper                   $viewHelper
     * @param Session                      $accountSession
     * @param CustomerRepositoryInterface  $customerRepository
     * @param ManagerInterface             $messageManager
     * @param ResponseInterface            $response
     * @param UrlInterface                 $urlBuilder
     */
    public function __construct(
        CustomerSession $customerSession,
        AuthenticationStateInterface $authenticationState,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        Session $accountSession,
        CustomerRepositoryInterface $customerRepository,
        ManagerInterface $messageManager,
        ResponseInterface $response,
        UrlInterface $urlBuilder
    ) {
        $this->customerSession = $customerSession;
        $this->authenticationState = $authenticationState;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->customerSession = $accountSession;
        $this->customerRepository = $customerRepository;
        $this->messageManager = $messageManager;
        $this->response = $response;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Perform customer authentication.
     *
     * @param ActionInterface  $subject
     * @param RequestInterface $request
     *
     * @return null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws NotFoundException
     */
    public function beforeDispatch(
        ActionInterface $subject,
        RequestInterface $request
    ) {
        if ($this->authenticationState->isEnabled() &&
            !$this->customerSession->authenticate()
        ) {
            $subject->getActionFlag()->set('', 'no-dispatch', true);

            return null;
        }

        if ($this->moduleConfig->isEnabled() === false) {
            throw new NotFoundException(__('Extension is disabled.'));
        }

        if ($this->viewHelper->isSubaccountLoggedIn()) {
            throw new NotFoundException(
                __('Subaccount does not have permission to view this page.')
            );
        }

        if ($this->customerSession
            && $request->getOriginalPathInfo() !== '/subaccounts/manage/'
        ) {
            $customerId = $this->customerSession->getCustomerData()->getId();
            $customer = $this->customerRepository->getById($customerId);

            $canManageSubaccounts = $customer->getCustomAttribute('can_manage_subaccounts');
            if ($canManageSubaccounts !== null) {
                $canManageSubaccounts = $canManageSubaccounts->getValue();
            } else {
                $canManageSubaccounts = $this->moduleConfig
                    ->canParentAccountManageSubaccounts();
            }

            if (!$canManageSubaccounts) {
                $this->messageManager->addErrorMessage(
                    __('You don\'t have permission to edit / create sub account.')
                );

                $this->response->setRedirect(
                    $this->urlBuilder->getUrl(
                        'subaccounts/manage/'
                    )
                );
            }
        }

        return null;
    }
}
