<?php

namespace Kemana\Shippingrestriction\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'kemana_shippingrestriction_rule'
         */

        $table = $installer->getConnection()
                           ->newTable($installer->getTable('kemana_shippingrestriction_rule'))
                           ->addColumn(
                               'rule_id',
                               \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                               null,
                               ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                               'Rule Id'
                           )
                           ->addColumn(
                               'for_admin',
                               \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                               null,
                               ['unsigned' => true, 'nullable' => false],
                               'For Admin'
                           )
                           ->addColumn(
                               'is_active',
                               \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                               null,
                               ['unsigned' => true, 'nullable' => false],
                               'Active'
                           )
                           ->addColumn(
                               'all_stores',
                               \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                               null,
                               ['unsigned' => true, 'nullable' => false],
                               'All Stores'
                           )
                           ->addColumn(
                               'all_groups',
                               \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                               null,
                               ['unsigned' => true, 'nullable' => false],
                               'All Groups'
                           )
                           ->addColumn(
                               'name',
                               \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                               null,
                               ['nullable' => false],
                               'Name'
                           )
                           ->addColumn(
                               'out_of_stock',
                               \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                               null,
                               ['nullable' => false],
                               'Out Of Stock'
                           )
                           ->addColumn(
                               'days',
                               \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                               null,
                               ['nullable' => false],
                               'Days'
                           )
                           ->addColumn(
                               'stores',
                               \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                               null,
                               ['nullable' => true, 'nullable' => false],
                               'Stores'
                           )
                           ->addColumn(
                               'cust_groups',
                               \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                               null,
                               ['nullable' => true, 'nullable' => false],
                               'Groups'
                           )
                           ->addColumn(
                               'message',
                               \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                               null,
                               ['nullable' => true],
                               'Message'
                           )
                           ->addColumn(
                               'methods',
                               \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                               null,
                               ['nullable' => true],
                               'Methods'
                           )
                           ->setComment('Kemana Shippingrestriction Rule');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'kemana_shippingrestriction_attribute'
         */

        $table = $installer->getConnection()
                           ->newTable($installer->getTable('kemana_shippingrestriction_attribute'))
                           ->addColumn(
                               'attr_id',
                               \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                               null,
                               ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                               'Attribute Id'
                           )
                           ->addColumn(
                               'rule_id',
                               \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                               null,
                               ['nullable' => false, 'unsigned' => true],
                               'Rule Id'
                           )
                           ->addColumn(
                               'code',
                               \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                               null,
                               ['nullable' => false],
                               'Code'
                           )
                           ->setComment('Kemana Shippingrestriction Attribute');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
