<?php

namespace Kemana\AdvancedInventory\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    public function install(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        //START: install stuff
        //END:   install stuff

        if ($installer->tableExists('kemana_configuable')) {
            $installer->getConnection()->dropTable('kemana_configuable');
        }
        //START table setup
        $table = $installer->getConnection()->newTable(
            $installer->getTable('kemana_configuable')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '10',
            ['nullable' => false, 'unsigned' => true, 'primary' => true, 'identity' => true],
            'Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '10',
            ['nullable' => false],
            'Store Id'
        )->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            '10',
            ['nullable' => false],
            'Customer Group Id'
        )->addColumn(
            'content',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Content Json Data'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
