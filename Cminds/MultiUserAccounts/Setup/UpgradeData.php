<?php

namespace Cminds\MultiUserAccounts\Setup;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;

    /**
     * @var Config
     */
    protected $resourceConfig;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     * @param Config $resourceConfig
     * @param ScopeConfigInterface $scopeConfig
     * @param IndexerRegistry $indexerRegistry
     * @param EavConfig $eavConfig
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        Config $resourceConfig,
        ScopeConfigInterface $scopeConfig,
        IndexerRegistry $indexerRegistry,
        EavConfig $eavConfig
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->resourceConfig = $resourceConfig;
        $this->scopeConfig = $scopeConfig;
        $this->eavConfig = $eavConfig;
        $this->indexerRegistry = $indexerRegistry;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $customerSetup->addAttribute(
                Customer::ENTITY,
                'can_manage_subaccounts',
                [
                    'type' => 'int',
                    'label' => __('Can Manage Subaccounts'),
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'required' => false,
                    'default' => 0,
                    'visible' => true,
                    'admin_only' => true,
                    'system' => 0,
                ]
            );
            $customerSetup
                ->getEavConfig()
                ->getAttribute(
                    'customer',
                    'can_manage_subaccounts'
                )
                ->setData('used_in_forms', ['adminhtml_customer'])
                ->save();

            $customerSetup->addAttribute(
                Customer::ENTITY,
                'customer_is_active',
                [
                    'type' => 'int',
                    'label' => __('Is Customer Active'),
                    'input' => 'select',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'required' => false,
                    'default' => 1,
                    'visible' => true,
                    'admin_only' => true,
                    'system' => 0,
                ]
            );
            $customerSetup
                ->getEavConfig()
                ->getAttribute(
                    'customer',
                    'customer_is_active'
                )
                ->setData('used_in_forms', ['adminhtml_customer'])
                ->save();

        }
        $indexer = $this->indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
        $indexer->reindexAll();

        $this->eavConfig->clear();

        $setup->endSetup();
    }

}