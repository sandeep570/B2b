<?php
// TODO: Refactoring.
// Instead of overriding object we can execute this logic as plugin.
namespace Cminds\MultiUserAccounts\CustomerData;

use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Customer\Model\Session\Proxy as CustomerSession;

/**
 * Cminds MultiUserAccounts customer section source object.
 *
 * @category    Cminds
 * @package     Cminds_MultiUserAccounts
 * @author      Piotr Pierzak <piotr@cminds.com>
 */
class Customer extends \Magento\Customer\CustomerData\Customer
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var CustomerViewHelper
     */
    protected $customerViewHelper;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * @var ModuleConfig
     */
    protected $moduleConfig;

    /**
     * @var ViewHelper
     */
    protected $viewHelper;

    /**
     * Object initialization.
     *
     * @param   CurrentCustomer $currentCustomer
     * @param   CustomerViewHelper $customerViewHelper
     * @param   CustomerSession $customerSession
     * @param   ModuleConfig $moduleConfig
     * @param  ViewHelper $viewHelper
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        CustomerViewHelper $customerViewHelper,
        CustomerSession $customerSession,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->customerViewHelper = $customerViewHelper;
        $this->customerSession = $customerSession;
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;

        parent::__construct(
            $currentCustomer,
            $customerViewHelper
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return [];
        }

        if ($this->moduleConfig->isEnabled() === false ||
            $this->moduleConfig->isSharedSessionEnabled() === false ||
            $this->viewHelper->isSubaccountLoggedIn() === false
        ) {
            return parent::getSectionData();
        }

        $subaccountDataObject = $this->customerSession->getSubaccountData();
        $subaccountName = $this->viewHelper
            ->getSubaccountName($subaccountDataObject);

        return [
            'fullname' => $subaccountName,
            'firstname' => $subaccountDataObject->getFirstname(),
        ];
    }
}
