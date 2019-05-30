<?php

namespace Cminds\MultiUserAccounts\Model\Plugin\Customer\Customer;

use Magento\Customer\Model\Customer;
use Magento\Framework\Registry;
use Cminds\MultiUserAccounts\Model\Import;

/**
 * Cminds MultiUserAccounts customer model plugin.
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
     * @param Customer $subject
     * @param \Closure $proceed
     * @param string   $type
     * @param string   $backUrl
     * @param string   $storeId
     *
     * @return Customer|mixed
     */
    public function aroundSendNewAccountEmail(
        Customer $subject,
        \Closure $proceed,
        $type = 'registered',
        $backUrl = '',
        $storeId = '0'
    ) {
        if ($this->coreRegistry->registry(Import::SKIP_CUSTOMER_WELCOME_EMAIL)) {
            return $subject;
        }

        $proceed($type, $backUrl, $storeId);

        return $subject;
    }
}
