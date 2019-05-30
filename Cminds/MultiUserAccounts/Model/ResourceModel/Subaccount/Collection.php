<?php

namespace Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Cminds MultiUserAccounts subaccount collection resource model.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class Collection extends AbstractCollection
{
    /**
     * Resource model construct that should be used for object initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_init(
            'Cminds\MultiUserAccounts\Model\Subaccount',
            'Cminds\MultiUserAccounts\Model\ResourceModel\Subaccount'
        );
    }

    /**
     * @param   int $parentCustomerId
     * @return  Collection
     */
    public function filterByParentCustomerId($parentCustomerId)
    {
        $this
            ->getSelect()
            ->where('parent_customer_id = ?', $parentCustomerId);

        return $this;
    }

    /**
     * @param   int $parentCustomerId
     * @return  Collection
     */
    public function filterByCustomerId($customerId)
    {
        $this
            ->getSelect()
            ->where('customer_id = ?', $customerId);

        return $this;
    }
}
