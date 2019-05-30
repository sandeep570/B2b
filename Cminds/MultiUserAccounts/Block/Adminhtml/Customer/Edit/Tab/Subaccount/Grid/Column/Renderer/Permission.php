<?php

namespace Cminds\MultiUserAccounts\Block\Adminhtml\Customer\Edit\Tab\Subaccount\Grid\Column\Renderer;

use Cminds\MultiUserAccounts\Model\Permission as PermissionModel;
use Cminds\MultiUserAccounts\Model\ResourceModel\SubaccountRepository;
use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Cminds MultiUserAccounts subaccounts grid permission renderer block.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Permission extends AbstractRenderer
{
    /**
     * @var Permission
     */
    private $permission;

    /**
     * @var SubaccountRepository
     */
    private $subaccountRepository;

    /**
     * Object initialization.
     *
     * @param Context              $context
     * @param PermissionModel      $permission
     * @param SubaccountRepository $subaccountRepository
     * @param array                $data
     */
    public function __construct(
        Context $context,
        PermissionModel $permission,
        SubaccountRepository $subaccountRepository,
        array $data = []
    ) {
        $this->permission = $permission;
        $this->subaccountRepository = $subaccountRepository;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @param DataObject $row
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getValue(DataObject $row)
    {
        $subaccountDataObject = $this->subaccountRepository
            ->getById($row->getId());

        return $this->permission
            ->getSubaccountPermissionDescriptionHtml($subaccountDataObject);
    }
}
