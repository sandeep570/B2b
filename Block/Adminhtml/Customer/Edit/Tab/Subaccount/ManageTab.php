<?php

namespace Cminds\MultiUserAccounts\Block\Adminhtml\Customer\Edit\Tab\Subaccount;

use Cminds\MultiUserAccounts\Model\ResourceModel\SubaccountRepository;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Cminds MultiUserAccounts customer edit tab block to manage subaccounts.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class ManageTab extends Template implements TabInterface
{
    /**
     * Core registry object.
     *
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Subaccount repository object.
     *
     * @var SubaccountRepository
     */
    private $subaccountRepository;

    /**
     * Object initialization.
     *
     * @param Context              $context Context object.
     * @param Registry             $registry Registry object.
     * @param SubaccountRepository $subaccountRepository Subaccount repository
     *                                                   object.
     * @param array                $data Array data.
     */
    public function __construct(
        Context $context,
        Registry $registry,
        SubaccountRepository $subaccountRepository,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->subaccountRepository = $subaccountRepository;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Return customer id.
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->coreRegistry
            ->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * Return tab label.
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Manage Subaccounts');
    }

    /**
     * Return tab title.
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Manage Subaccounts');
    }

    /**
     * Return bool value if tab can be displayed or not.
     *
     * @return bool
     */
    public function canShowTab()
    {
        $customerId = $this->getCustomerId();
        if ($customerId) {
            try {
                $this->subaccountRepository
                    ->getByCustomerId($customerId);
            } catch (NoSuchEntityException $e) {
                return $this->_authorization->isAllowed(
                    'Cminds_MultiUserAccounts::manage_subaccounts'
                );
            }
        }

        return false;
    }

    /**
     * Return bool value if tab is hidden or not.
     *
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }

        return true;
    }

    /**
     * Tab class getter.
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content.
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('subaccounts/manage/index', ['_current' => true]);
    }

    /**
     * Tab should be loaded trough Ajax call.
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }
}
