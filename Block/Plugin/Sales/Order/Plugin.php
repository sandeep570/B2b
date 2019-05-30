<?php

namespace Cminds\MultiUserAccounts\Block\Plugin\Sales\Order;

use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount as SubaccountResourceModel;
use Magento\Customer\Model\Session\Proxy as CustomerSession;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Sales\Model\Order\Config as OrderConfig;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

/**
 * Cminds MultiUserAccounts recent sales order history block plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    /**
     * Order collection object.
     *
     * @var OrderCollection
     */
    protected $orders;

    /**
     * Session object.
     *
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Order collection factory object.
     *
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * Module config object.
     *
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * View helper object.
     *
     * @var ViewHelper
     */
    protected $viewHelper;

    /**
     * Order config object.
     *
     * @var OrderConfig
     */
    protected $orderConfig;

    /**
     * Subaccount resource model object.
     *
     * @var SubaccountResourceModel
     */
    protected $subaccountResourceModel;

    /**
     * Object initialization..
     *
     * @param CustomerSession         $customerSession Session object.
     * @param OrderCollectionFactory  $orderCollectionFactory Order collection
     *     factory object.
     * @param ModuleConfig            $moduleConfig Module config object.
     * @param ViewHelper              $viewHelper View helper object.
     * @param OrderConfig             $orderConfig Order config object.
     * @param SubaccountResourceModel $subaccountResourceModel Subaccount
     *     resource model object.
     */
    public function __construct(
        CustomerSession $customerSession,
        OrderCollectionFactory $orderCollectionFactory,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        OrderConfig $orderConfig,
        SubaccountResourceModel $subaccountResourceModel
    ) {
        $this->customerSession = $customerSession;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->orderConfig = $orderConfig;
        $this->subaccountResourceModel = $subaccountResourceModel;
    }

    /**
     * Around getOrders plugin.
     *
     * @param BlockInterface $subject Subject object.
     * @param \Closure       $proceed Closure.
     * @param string         $key Key.
     * @param mixed          $index Index.
     *
     * @return OrderCollection|bool
     */
    public function aroundGetData(
        BlockInterface $subject,
        \Closure $proceed,
        $key = '',
        $index = null
    ) {
        if ($key !== 'orders') {
            return $proceed($key, $index);
        }

        if ($this->moduleConfig->isEnabled() === false) {
            return $proceed($key, $index);
        }

        if ($this->orders === null) {
            $this->orders = $this
                ->getOrders()
                ->setPageSize('5')
                ->load();
        }

        return $this->orders;
    }

    /**
     * Return order collection.
     *
     * @return OrderCollection|bool
     */
    protected function getOrders()
    {
        $customerId = $this->customerSession->getCustomerId();
        if (!$customerId) {
            return false;
        }

        $collection = $this->orderCollectionFactory->create()
            ->addFieldToSelect(
                '*'
            )
            ->addFieldToFilter(
                'status',
                ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
            )
            ->setOrder(
                'created_at',
                'desc'
            );

        /** @var SubaccountTransportInterface $subaccountTransportDataObject */
        $subaccountTransportDataObject = $this->customerSession
            ->getSubaccountData();

        $getAllOrders = false;
        $parentCustomerId = $customerId;

        if ($this->viewHelper->isSubaccountLoggedIn() === false) {
            $getAllOrders = true;
        }

        $orderHistoryViewPermission = false;
        if ($this->viewHelper->isSubaccountLoggedIn() === true) {
            $orderHistoryViewPermission = (bool)$subaccountTransportDataObject
                ->getAccountOrderHistoryViewPermission();

            if ($this->moduleConfig->isSharedSessionEnabled() === true) {
                $parentCustomerId = $subaccountTransportDataObject
                    ->getCustomerId();
            }
        }

        if ($orderHistoryViewPermission === true) {
            $getAllOrders = true;

            $parentCustomerId = $subaccountTransportDataObject
                ->getParentCustomerId();
        }

        $subaccountIds = [];
        if ($getAllOrders) {
            $subaccountIds = $this->subaccountResourceModel
                ->getSubaccountIdsByParentCustomerId($parentCustomerId);
        }

        if ($getAllOrders && count($subaccountIds)) {
            $subaccountIdsStr = implode(',', $subaccountIds);

            $collection->getSelect()
                ->where('(customer_id = ?', $parentCustomerId)
                ->orWhere('subaccount_id in(' . $subaccountIdsStr . '))');
        } else {
            $collection->addFieldToFilter(
                'customer_id',
                $parentCustomerId
            );
        }

        return $collection;
    }
}
