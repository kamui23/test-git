<?php 
namespace Icube\Order\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'delivery_pickup',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'delivery_pickup',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'store_code',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'delivery_pickup',
            ]
        );

       $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'delivery_pickup',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'delivery_pickup',
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'store_code',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'delivery_pickup',
            ]
        );


        $installer->endSetup();
    }

}
