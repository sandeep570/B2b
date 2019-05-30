<?php

namespace Cminds\MultiUserAccounts\Block\Manage;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as HelperView;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Cminds\MultiUserAccounts\Model\Permission;
use Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount\CollectionFactory as SubaccountCollectionFactory;
use Cminds\MultiUserAccounts\Model\ResourceModel\SubaccountTransportRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Cminds\MultiUserAccounts\Helper\Manage as ManageHelper;

/**
 * Cminds MultiUserAccounts manage list block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Table extends Template
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var SubaccountCollectionFactory
     */
    private $subaccountCollectionFactory;

    /**
     * @var HelperView
     */
    private $helperView;

    /**
     * @var Permission
     */
    private $permission;

    /**
     * @var SubaccountTransportInterface[]
     */
    private $subaccounts;

    /**
     * @var SubaccountTransportRepository
     */
    private $subaccountTransportRepository;


    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var ManageHelper
     */
    private $manageHelper;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * Object initialization.
     *
     * @param Context                       $context
     * @param SubaccountCollectionFactory   $subaccountCollectionFactory
     * @param CustomerSession               $customerSession
     * @param HelperView                    $helperView
     * @param Permission                    $permission
     * @param SubaccountTransportRepository $subaccountTransportRepository
     * @param ModuleConfig                  $moduleConfig
     * @param array                         $data
     */
    public function __construct(
        Context $context,
        SubaccountCollectionFactory $subaccountCollectionFactory,
        CustomerSession $customerSession,
        HelperView $helperView,
        Permission $permission,
        SubaccountTransportRepository $subaccountTransportRepository,
        CustomerRepositoryInterface $customerRepository,
        ManageHelper $manageHelper,
        ModuleConfig $moduleConfig,
        array $data = []
    ) {
        $this->subaccountCollectionFactory = $subaccountCollectionFactory;
        $this->customerSession = $customerSession;
        $this->helperView = $helperView;
        $this->permission = $permission;
        $this->subaccountTransportRepository = $subaccountTransportRepository;
        $this->customerRepository = $customerRepository;
        $this->manageHelper = $manageHelper;
        $this->moduleConfig = $moduleConfig;

        parent::__construct($context, $data);
    }

    public function getCanManageSubaccounts()
    {
        return $this->manageHelper->getCanManageSubaccounts();
    }

    /**
     * @return bool
     */
    public function canManageSubaccounts()
    {
        /** @var Customer $customer */
        $customer = $this->customerSession->getCustomer();

        /** @var CustomerInterface $customer */
        $customer = $customer->getDataModel();

        $canManageSubaccounts = $customer->getCustomAttribute('can_manage_subaccounts');
        if ($canManageSubaccounts !== null) {
            $canManageSubaccounts = (int)$canManageSubaccounts->getValue();
        } else {
            $canManageSubaccounts = (int)$this->moduleConfig
                ->canParentAccountManageSubaccounts();
        }

        return (bool)$canManageSubaccounts;
    }

    /**
     * @return SubaccountTransportInterface[]|bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubaccounts()
    {
        $customerId = $customerId = $this->customerSession->getCustomerId();
        if ($customerId === null) {
            return false;
        }

        if (!$this->subaccounts) {
            $collection = $this->subaccountCollectionFactory->create()
                ->addFieldToSelect(
                    '*'
                )
                ->addFieldToFilter(
                    'parent_customer_id',
                    $customerId
                )
                ->setOrder(
                    'created_at',
                    'desc'
                );

            foreach ($collection as $subaccount) {
                $this->subaccounts[] = $this->subaccountTransportRepository
                    ->getById($subaccount->getId());
            }
        }

        return $this->subaccounts;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($this->getCustomers()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'subaccounts.manage.table.pager'
            )->setCollection(
                $this->getCustomers()
            );
            $this->setChild('pager', $pager);
            $this->getCustomers()->load();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param   SubaccountTransportInterface $subaccount
     *
     * @return  string
     */
    public function getEditUrl(SubaccountTransportInterface $subaccount)
    {
        return $this->getUrl(
            'subaccounts/manage/edit',
            ['id' => $subaccount->getId()]
        );
    }

    /**
     * @param   SubaccountTransportInterface $subaccount
     *
     * @return  string
     */
    public function getDeleteUrl(SubaccountTransportInterface $subaccount)
    {
        return $this->getUrl(
            'subaccounts/manage/delete',
            ['id' => $subaccount->getId()]
        );
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    /**
     * Concatenate all subaccount name parts into full subaccount name.
     *
     * @param   SubaccountTransportInterface $subaccount
     *
     * @return  string
     */
    public function getSubaccountName(SubaccountTransportInterface $subaccount)
    {
        return $this->helperView->getSubaccountName($subaccount);
    }

    /**
     * Return subaccount status phrase.
     *
     * @param   SubaccountTransportInterface $subaccount
     *
     * @return  \Magento\Framework\Phrase
     */
    public function getSubaccountStatus(
        SubaccountTransportInterface $subaccount
    ) {
        $customerId = $subaccount->getCustomerId();

        if ($this->getIsActive($customerId)) {
            return __('Active');
        }
        else {
            return __('Inactive');
        }
        return $subaccount->getIsActive() ? __('Active') : __('Inactive');
    }

    /**
     * Get Customer Entity by id.
     *
     * @param $id
     * @return mixed
     */
    private function getCustomerById($id)
    {
        return $this->customerRepository->getById($id);
    }

    /**
     * Retrive the is active custom customer attribute.
     *
     * @param $customerId
     * @return int
     */
    public function getIsActive($customerId)
    {
        $customer = $this->getCustomerById($customerId);
        $customerActive = $customer->getCustomAttribute('customer_is_active');
        if (isset($customerActive)) {
            $is_active = $customerActive->getValue();
        } else {
            $is_active = 0;
        }

        return $is_active;
    }

    /**
     * Return subaccount permission description html.
     *
     * @param   SubaccountTransportInterface $subaccount
     *
     * @return  string
     */
    public function getSubaccountPermissionDescriptionHtml(
        SubaccountTransportInterface $subaccount
    ) {
        return $this->permission
            ->getSubaccountPermissionDescriptionHtml($subaccount);
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecured.
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl(
            'subaccounts/manage/add',
            ['_secure' => true]
        );
    }
}
