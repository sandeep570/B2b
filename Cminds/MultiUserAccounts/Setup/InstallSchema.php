<?php

namespace Cminds\MultiUserAccounts\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Cminds MultiUserAccounts install schema.
 *
 * @category Cminds
 * @package  Cminds_MultiUserAccounts
 * @author   Piotr Pierzak <piotr@cminds.com>
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        /**
         * Create cminds_multiuseraccounts_subaccount table.
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('cminds_multiuseraccounts_subaccount'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Entity ID'
            )
            ->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Customer ID'
            )
            ->addColumn(
                'parent_customer_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Parent Customer ID'
            )
            ->addColumn(
                'permission',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0,
                ],
                'Permission'
            )
            ->addColumn(
                'is_active',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'default' => 0,
                ],
                'Is Active'
            )
            ->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT,
                ],
                'Created At'
            )
            ->addColumn(
                'updated_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT_UPDATE,
                ],
                'Updated At'
            )
            ->addForeignKey(
                $setup->getFkName(
                    'cminds_multiuseraccounts_subaccount',
                    'customer_id',
                    'customer_entity',
                    'entity_id'
                ),
                'customer_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName(
                    'cminds_multiuseraccounts_subaccount',
                    'parent_customer_id',
                    'customer_entity',
                    'entity_id'
                ),
                'parent_customer_id',
                $setup->getTable('customer_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('Cminds MultiUserAccounts Customer Entity');
        $setup->getConnection()->createTable($table);

        /**
         * Add subaccount_id column to sales_order table.
         */
        $table = $setup->getTable('sales_order');
        $setup->getConnection()->addColumn(
            $table,
            'subaccount_id',
            [
                'type' => Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => true,
                'comment' => 'Subaccount ID',
            ]
        );

        /**
         * Add is_approved column to quote table.
         */
        $table = $setup->getTable('quote');
        $setup->getConnection()->addColumn(
            $table,
            'is_approved',
            [
                'type' => Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Is Approved',
            ]
        );
        $setup->getConnection()->addColumn(
            $table,
            'approve_hash',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'default' => null,
                'comment' => 'Approve Hash',
            ]
        );

        $setup->endSetup();
    }
}
