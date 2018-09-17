<?php

namespace Icube\Brands\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

Class UpgradeSchema implements UpgradeSchemaInterface
{
  public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
  {
      $installer = $setup;
      $setup->startSetup();
      $connection = $setup->getConnection();
      $version = $context->getVersion();
      /**
       * Create table 'kemana_brands_store'
       */
      $installer->getConnection()->dropTable($installer->getTable('kemana_brands_store'));
      $table_kemana_brands_store = $installer->getConnection()
      ->newTable($installer->getTable('kemana_brands_store'))
      ->addColumn(
          'ib_id',
          Table::TYPE_INTEGER,
          10,
          ['nullable' => false, 'primary' => true],
          'icube brands Id'
      )->addColumn(
          'store_id',
          Table::TYPE_SMALLINT,
          null,
          ['unsigned' => true, 'nullable' => false, 'primary' => true],
          'Store ID'
      )->addIndex(
          $installer->getIdxName('kemana_brands_store', ['store_id']),
          ['store_id']
      )->setComment(
          'Kemana Brands Store Table'
      );
      $installer->getConnection()->createTable($table_kemana_brands_store);

      /**
       * Create table 'kemana_brands_customer'
       */
      $installer->getConnection()->dropTable($installer->getTable('kemana_brands_customer'));
      $table_kemana_brands_customerGroup = $installer->getConnection()
      ->newTable($installer->getTable('kemana_brands_customer'))
      ->addColumn(
          'ib_id',
          Table::TYPE_INTEGER,
          10,
          ['nullable' => false, 'primary' => true],
          'icube brands Id'
      )->addColumn(
          'customer_group_id',
          Table::TYPE_SMALLINT,
          null,
          ['unsigned' => true, 'nullable' => false, 'primary' => true],
          'Customer Group Id'
      )->addIndex(
          $installer->getIdxName('kemana_brands_customer', ['customer_group_id']),
          ['customer_group_id']
      )->addForeignKey(
          $installer->getFkName(
              'kemana_brands_customer',
              'customer_group_id',
              'customer_group',
              'customer_group_id'
          ),
          'customer_group_id',
          'customer_group',
          'customer_group_id',
          Table::ACTION_CASCADE
      )->setComment(
          'Kemana Brand customer Table'
      );
      $installer->getConnection()->createTable($table_kemana_brands_customerGroup);
      $setup->endSetup();
  }
}

?>
