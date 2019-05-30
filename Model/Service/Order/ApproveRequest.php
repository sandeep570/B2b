<?php

namespace Cminds\MultiUserAccounts\Model\Service\Order;

use Magento\Quote\Model\Quote;
use Cminds\MultiUserAccounts\Api\SubaccountTransportRepositoryInterface;
use Cminds\MultiUserAccounts\Api\Data\SubaccountTransportInterface;
use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Magento\Customer\Model\CustomerRegistry;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount\Collection as SubaccountCollection;
use Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount\CollectionFactory as SubaccountCollectionFactory;
use Cminds\MultiUserAccounts\Api\Data\SubaccountInterface;
use Cminds\MultiUserAccounts\Helper\Email as EmailHelper;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\UrlInterface;

/**
 * Cminds MultiUserAccounts approve request service.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class ApproveRequest
{
    /**
     * @var SubaccountTransportRepositoryInterface
     */
    private $subaccountTransportRepository;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * @var EmailHelper
     */
    private $emailHelper;

    /**
     * @var SubaccountCollectionFactory
     */
    private $subaccountCollectionFactory;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $subaccountRanges = [];

    /**
     * ApproveRequest constructor.
     *
     * @param SubaccountTransportRepositoryInterface $subaccountTransportRepository
     * @param CustomerRegistry                       $customerRegistry
     * @param ModuleConfig                           $moduleConfig
     * @param ViewHelper                             $viewHelper
     * @param EmailHelper                            $emailHelper
     * @param SubaccountCollectionFactory            $subaccountCollectionFactory
     * @param DataObjectFactory                      $dataObjectFactory
     * @param UrlInterface                           $urlBuilder
     */
    public function __construct(
        SubaccountTransportRepositoryInterface $subaccountTransportRepository,
        CustomerRegistry $customerRegistry,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        EmailHelper $emailHelper,
        SubaccountCollectionFactory $subaccountCollectionFactory,
        DataObjectFactory $dataObjectFactory,
        UrlInterface $urlBuilder
    ) {
        $this->subaccountTransportRepository = $subaccountTransportRepository;
        $this->customerRegistry = $customerRegistry;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;
        $this->emailHelper = $emailHelper;
        $this->subaccountCollectionFactory = $subaccountCollectionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @param Quote $quoteModel
     * @param       $subaccount
     *
     * @return bool
     */
    public function canAuthorize(Quote $quoteModel, $subaccount)
    {
        $authorizers = $this->getAuthorizers($quoteModel);

        foreach ($authorizers as $authorizerSubaccount) {
            if ($authorizerSubaccount->getId() === $subaccount->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Quote $quoteModel
     *
     * @return array
     */
    public function getAuthorizers(Quote $quoteModel)
    {
        $parentCustomerId = $this->getParentAccountId($quoteModel);

        $quoteGrandTotal = (float)$quoteModel->getBaseGrandTotal();
        $authorizedLevel = (float)$quoteModel->getAuthorizedRange();

        $subaccountRanges = $this->getSubaccountRanges($parentCustomerId);
        $matchedSubaccounts = [];

        foreach ($subaccountRanges as $allowedAmount => $subaccounts) {
            if ($allowedAmount <= $authorizedLevel) {
                continue;
            }
            if ($allowedAmount > $quoteGrandTotal) {
                continue;
            }

            $matchedSubaccounts = $subaccounts;
            break;
        }

        return $matchedSubaccounts;
    }

    public function getApprovers(Quote $quoteModel)
    {
        $parentCustomerId = $this->getParentAccountId($quoteModel);

        $quoteGrandTotal = (float)$quoteModel->getBaseGrandTotal();

        $subaccountRanges = $this->getSubaccountRanges($parentCustomerId);
        $matchedSubaccounts = [];

        foreach ($subaccountRanges as $allowedAmount => $subaccounts) {
            if ($allowedAmount < $quoteGrandTotal) {
                continue;
            }

            $matchedSubaccounts = $subaccounts;
            break;
        }

        return $matchedSubaccounts;
    }

    /**
     * @param Quote $quoteModel
     *
     * @return int
     */
    private function getParentAccountId(Quote $quoteModel)
    {
        $subaccountId = $quoteModel->getSubaccountId();

        /** @var SubaccountTransportInterface $reqSubaccountTransportDataObject */
        $reqSubaccountTransportDataObject = $this->subaccountTransportRepository
            ->getByCustomerId($subaccountId);

        $parentCustomerId = $reqSubaccountTransportDataObject
            ->getParentCustomerId();

        return $parentCustomerId;
    }

    /**
     * @param Quote $quoteModel
     *
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function processNotification(Quote $quoteModel)
    {
        $subaccountId = $quoteModel->getSubaccountId();

        /** @var SubaccountTransportInterface $reqSubaccountTransportDataObject */
        $reqSubaccountTransportDataObject = $this->subaccountTransportRepository
            ->getByCustomerId($subaccountId);

        $parentCustomerId = $reqSubaccountTransportDataObject
            ->getParentCustomerId();

        /** @var \Magento\Customer\Model\Customer $parentCustomerModel */
        $parentCustomerModel = $this->customerRegistry
            ->retrieve($parentCustomerId);

        $authorizationRequired = $this->moduleConfig
            ->isOrderApprovalRequestAuthorizationRequired();

        $isAuthorized = (int)$quoteModel->getIsAuthorized();

        if ($authorizationRequired === false) {
            $isAuthorized = 1;
        }

        $reqSubaccountName = $this->viewHelper
            ->getSubaccountName($reqSubaccountTransportDataObject);

        /**
         * Send approval request notification to matching sub-accounts.
         */
        $subaccountRanges = $this->getSubaccountRanges($parentCustomerId);
        ksort($subaccountRanges);

        $matchedSubaccounts = [];

        // Looking for authorizers if not authorized.
        if ($isAuthorized === 0) {
            $matchedSubaccounts = $this->getAuthorizers($quoteModel);
        }

        // Looking for approver in range if no more authorizers found
        // or is already authorized.
        if (empty($matchedSubaccounts)) {
            $matchedSubaccounts = $this->getApprovers($quoteModel);

            $isAuthorized = 1;
        }

        $hash = sha1(time() . rand());
        $quoteModel
            ->setApproveHash($hash)
            ->setIsAuthorized($isAuthorized)
            ->save();

        unset(
            $subaccountRanges,
            $allowedAmount,
            $subaccounts,
            $authorizationRequired
        );

        foreach ($matchedSubaccounts as $subaccount) {
            $subaccountName = $this->viewHelper
                ->getSubaccountName($subaccount);

            $this->sendOrderApprovalRequest(
                $subaccountName,
                $subaccount->getEmail(),
                $reqSubaccountName,
                $quoteModel->getId(),
                $hash,
                $isAuthorized
            );
        }

        /**
         * Send approval request to parent account.
         */
        $sendParentNotification = $this->moduleConfig
            ->shouldParentReceiveAllNotifications();

        if (empty($matchedSubaccounts)) {
            $sendParentNotification = true;
        }

        if ($sendParentNotification && $isAuthorized) {
            $this->sendOrderApprovalRequest(
                $parentCustomerModel->getName(),
                $parentCustomerModel->getEmail(),
                $reqSubaccountName,
                $quoteModel->getId(),
                $hash
            );
        }
    }

    /**
     * @param string $approverName
     * @param string $approverEmail
     * @param string $requesterName
     * @param int    $quoteId
     * @param string $hash
     * @param int    $isApproval
     *
     * @return ApproveRequest
     */
    private function sendOrderApprovalRequest(
        $approverName,
        $approverEmail,
        $requesterName,
        $quoteId,
        $hash,
        $isApproval = 1
    ) {
        $url = 'subaccounts/order/approve';
        if ($isApproval === 0) {
            $url = 'subaccounts/order/authorize';
        }

        $emailVariablesObject = $this->dataObjectFactory->create();
        $emailVariablesObject->setData([
            'approver_name' => $approverName,
            'requester_name' => $requesterName,
            'approve_url' => $this->urlBuilder->getUrl(
                $url,
                ['id' => $quoteId, 'hash' => $hash]
            ),
        ]);

        if ($isApproval) {
            $this->emailHelper->sendCheckoutOrderApproveRequestEmail(
                [
                    'name' => $approverName,
                    'email' => $approverEmail,
                ],
                ['data' => $emailVariablesObject]
            );
        } else {
            $this->emailHelper->sendCheckoutOrderAuthorizationRequestEmail(
                [
                    'name' => $approverName,
                    'email' => $approverEmail,
                ],
                ['data' => $emailVariablesObject]
            );
        }

        return $this;
    }

    /**
     * @param int $parentCustomerId
     *
     * @return array
     */
    private function getSubaccountRanges($parentCustomerId)
    {
        if (!isset($this->subaccountRanges[$parentCustomerId])) {
            /** @var SubaccountCollection $subaccountCollection */
            $subaccountCollection = $this->subaccountCollectionFactory
                ->create()
                ->addFieldToFilter(
                    'parent_customer_id',
                    $parentCustomerId
                );

            $subaccountRanges = [];
            foreach ($subaccountCollection as $subaccount) {
                /** @var SubaccountInterface $subaccountData */
                $subaccountTransportData = $this->subaccountTransportRepository
                    ->getById($subaccount->getId());

                // Checking permission.
                if (!$subaccountTransportData->getManageOrderApprovalPermission()) {
                    continue;
                }

                // Checking allowed approval amount.
                $allowedAmount = (float)$subaccountTransportData
                    ->getAdditionalInformationValue(
                        $subaccountTransportData::MANAGE_ORDER_APPROVAL_PERMISSION_AMOUNT
                    );

                if ($allowedAmount <= 0) {
                    $allowedAmount = 999999999;
                }

                $subaccountRanges[(int)$allowedAmount][] = $subaccountTransportData;

                $this->subaccountRanges[$parentCustomerId] = $subaccountRanges;
            }
        }

        return $this->subaccountRanges[$parentCustomerId];
    }
}
