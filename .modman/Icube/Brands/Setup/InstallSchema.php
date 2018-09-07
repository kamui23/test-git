<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;


class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        $table  = $installer->getConnection()
            ->newTable($installer->getTable('icube_brands_items'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'name',
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Name'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_INTEGER,
                null,
                ['default' => null , 'unique' => true],
                'attribute_id'
            )
            ->addIndex(
                $installer->getIdxName(
        					'attribute_id',
        					['attribute_id'],
        					\Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        				),
				        ['attribute_id'],
				        ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
			       )
            ->addColumn(
                'sort_order',
                Table::TYPE_INTEGER,
                null,
                ['default' => 0],
                'Sort Order'
            )
            ->addColumn(
                'category_url',
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'Category URL'
            )
            ->addColumn(
                'logo',
                Table::TYPE_TEXT,
                null,
                ['default' => null],
                'logo'
            )
            ->addColumn(
                    'is_active',
                    Table::TYPE_SMALLINT,
                    null,
                    [],
                    'Active Status'
            )
            ->addColumn(
                    'featured',
                    Table::TYPE_SMALLINT,
                    null,
                    ['default' => 0],
                    'Featured'
			)
			->setComment(
				'Brand Table'
			);
      $installer->endSetup();
    }
}
