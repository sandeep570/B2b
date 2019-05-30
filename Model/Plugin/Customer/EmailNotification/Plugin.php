<?php

namespace Cminds\MultiUserAccounts\Model\Plugin\Customer\EmailNotification;

use Magento\Customer\Model\EmailNotification;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Registry;
use Cminds\MultiUserAccounts\Model\Import;

/**
 * Cminds MultiUserAccounts email notification model plugin.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Plugin
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Plugin constructor.
     *
     * @param Registry $coreRegistry
     */
    public function __construct(Registry $coreRegistry)
    {
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @param EmailNotification $subject
     * @param \Closure          $proceed
     * @param CustomerInterface $customer
     * @param string            $type
     * @param string            $backUrl
     * @param int               $storeId
     * @param null              $sendemailStoreId
     *
     * @return EmailNotification
     */
    public function aroundNewAccount(
        EmailNotification $subject,
        \Closure $proceed,
        CustomerInterface $customer,
        $type = EmailNotification::NEW_ACCOUNT_EMAIL_REGISTERED,
        $backUrl = '',
        $storeId = 0,
        $sendemailStoreId = null
    ) {
        if ($this->coreRegistry->registry(Import::SKIP_CUSTOMER_WELCOME_EMAIL)) {
            return $subject;
        }

        $proceed(
            $customer,
            $type,
            $backUrl,
            $storeId,
            $sendemailStoreId
        );

        return $subject;
    }
}
