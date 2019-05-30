<?php

namespace Cminds\MultiUserAccounts\Controller\Plugin\Permission\Customer\Account\LoginPost;

use Cminds\MultiUserAccounts\Api\SubaccountRepositoryInterface;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\CustomerFactory;

/**
 * Cminds MultiUserAccounts customer account login post controller plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    /**
     * @var ManagerInterface
     */
    private $customerFactory;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var SubaccountRepositoryInterface
     */
    private $subaccountRepository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Object initialization.
     *
     * @param ManagerInterface              $messageManager
     * @param ModuleConfig                  $moduleConfig
     * @param ResponseInterface             $response
     * @param UrlInterface                  $urlBuilder
     * @param SubaccountRepositoryInterface $subaccountRepository
     * @param CustomerRepositoryInterface   $customerRepository
     * @param CustomerFactory               $customerFactory
     */
    public function __construct(
        ManagerInterface $messageManager,
        ModuleConfig $moduleConfig,
        ResponseInterface $response,
        UrlInterface $urlBuilder,
        SubaccountRepositoryInterface $subaccountRepository,
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory
    ) {
        $this->messageManager = $messageManager;
        $this->moduleConfig = $moduleConfig;
        $this->response = $response;
        $this->urlBuilder = $urlBuilder;
        $this->subaccountRepository = $subaccountRepository;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Check if customer wants to login as subaccount.
     *
     * @param ActionInterface  $subject
     * @param RequestInterface $request
     *
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeDispatch(
        ActionInterface $subject,
        RequestInterface $request
    ) {
        if ($this->moduleConfig->isEnabled() === false) {
            return null;
        }

        if ($request->isPost() === false) {
            return null;
        }

        $login = $request->getPost('login');
        if (empty($login['username']) || empty($login['password'])) {
            return null;
        }

        try {
            /** @var CustomerInterface $customer */
            $customer = $this->customerRepository
                ->get($login['username']);

            $isActive = $canManage = $customer->getCustomAttribute('customer_is_active');
            if ($isActive !== null) {
                $isActive = (int)$isActive->getValue();
            } else {
                $isActive = 1;
            }

            if ($isActive === 0) {
                $subject->getActionFlag()->set('', 'no-dispatch', true);

                $this->messageManager->addErrorMessage(
                    __('Your account is not active.')
                );
                $this->response->setRedirect(
                    $this->urlBuilder->getUrl('customer/account/login')
                );
            }
            if ($this->subaccountRepository->getByCustomerId($customer->getId())) {
                $parentId = $this->subaccountRepository->getByCustomerId($customer->getId())->getParentCustomerId();

                $customerModel = $this->customerFactory->create();
                $customerModel->load($parentId);
                $isActive = (int)$customerModel->getCustomerIsActive();
                if ($isActive === 0) {
                    $subject->getActionFlag()->set('', 'no-dispatch', true);

                    $this->messageManager->addErrorMessage(
                        __('Your account parent account is not active.')
                    );
                    $this->response->setRedirect(
                        $this->urlBuilder->getUrl('customer/account/login')
                    );
                }
            }
        } catch (NoSuchEntityException $e) {
            // No action is required here.
        }

        return null;
    }
}
