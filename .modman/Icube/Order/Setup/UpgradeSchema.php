<?php namespace Icube\Order\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {

            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'delivery_pickup',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Delivery or Pickup'
                ]
            );
             $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'store_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Store Code'
                ]
            );
              $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'delivery_pickup',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Delivery or Pickup'
                ]
            );
             $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'store_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Store Code'
                ]
            );
             
        }
        if (version_compare($context->getVersion(), '1.0.11') < 0) {
             $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'store_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Store Code'
                ]
            );

            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'store_code',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length'    => 255,
                    'unsigned' => true,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Store Code'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.12') < 0) {
            $connection = $installer->getConnection();
            $connection->query('ALTER TABLE sales_order_item ALTER `qty_backordered` SET DEFAULT 0;');
            $connection->query('ALTER TABLE sales_order_item ALTER `qty_canceled` SET DEFAULT 0;');
            $connection->query('ALTER TABLE sales_order_item ALTER `qty_invoiced` SET DEFAULT 0;');
            $connection->query('ALTER TABLE sales_order_item ALTER `qty_ordered` SET DEFAULT 0;');
            $connection->query('ALTER TABLE sales_order_item ALTER `qty_refunded` SET DEFAULT 0;');
            $connection->query('ALTER TABLE sales_order_item ALTER `qty_shipped` SET DEFAULT 0;');
        }

        if (version_compare($context->getVersion(), '1.0.13') < 0) {
            $table = $installer->getConnection()
                ->newTable($installer->getTable('icube_admin_pos'))
                ->addColumn(
                    'entity_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Entity Id'
                )->addColumn(
                    'user_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'User ID'
                )->addColumn(
                    'store_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['identity' => false, 'nullable' => true, 'primary' => false],
                    'Store code'
                )->addIndex(
                    $installer->getIdxName(
                        'icube_admin_pos',
                        ['user_id'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    'user_id',
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                );
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }

}