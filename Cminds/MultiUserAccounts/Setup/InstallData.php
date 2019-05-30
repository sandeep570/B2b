<?php

namespace Cminds\MultiUserAccounts\Setup;

use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $customerSetupFactory;

    /**
     * Object constructor.
     *
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @param ModuleDataSetupInterface $setup Module data setup object.
     * @param ModuleContextInterface   $context Module context object.
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

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
            ->setData(
                'used_in_forms',
                ['adminhtml_customer']
            )
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
            ->setData(
                'used_in_forms',
                ['adminhtml_customer']
            )
            ->save();

        $setup->endSetup();
    }
}
