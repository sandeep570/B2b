<?php

namespace Cminds\MultiUserAccounts\Block\View\Element\Html\Link;

use Cminds\MultiUserAccounts\Helper\View as ViewHelper;
use Cminds\MultiUserAccounts\Model\Config as ModuleConfig;
use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Html\Link\Current;
use Magento\Framework\View\Element\Template\Context;

/**
 * Cminds MultiUserAccounts manage orders waiting for approval link block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class ManageOrderApprovals extends Current
{
    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var ViewHelper
     */
    private $viewHelper;

    /**
     * Object initialization.
     *
     * @param   Context              $context
     * @param   DefaultPathInterface $defaultPath
     * @param   ModuleConfig         $moduleConfig
     * @param   ViewHelper           $viewHelper
     * @param   array                $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        ModuleConfig $moduleConfig,
        ViewHelper $viewHelper,
        array $data = []
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->viewHelper = $viewHelper;

        parent::__construct(
            $context,
            $defaultPath,
            $data
        );
    }

    /**
     * Render block HTML.
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->moduleConfig->isEnabled() === false) {
            return '';
        }

        if ($this->viewHelper->isSubaccountLoggedIn()
            && $this->viewHelper->canManageOrderApprovals() === false
        ) {
            return '';
        }

        return parent::_toHtml();
    }
}
