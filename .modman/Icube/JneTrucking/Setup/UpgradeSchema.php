<?php

namespace Icube\JneTrucking\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
	
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$setup->startSetup();
		if (version_compare($context->getVersion(), '1.0.1') < 0) {
			$tableName = $setup->getTable('icube_jnetrucking');

			if ($setup->getConnection()->isTableExists($tableName) == true) {
				$connection = $setup->getConnection();

				$connection->addColumn(
					$tableName,
					'weight_min',			
                    [
                    	'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    	'nullable' => false,
                    	'default' => '0',
                    	'comment' => 'Minimum Weight'
                    ]
                );
			}
		}
		$setup->endSetup();
	}
}