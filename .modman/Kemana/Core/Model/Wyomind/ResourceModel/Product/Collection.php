<?php

namespace Kemana\Core\Model\Wyomind\ResourceModel\Product;

class Collection extends \Wyomind\AdvancedInventory\Model\ResourceModel\Product\Collection
{
    public function getStockSettings($productId, $placeId, $itemId, $placeIds)
    {
        $connection = $this->_resource;

        $pointofsale = $connection->getTableName("pointofsale");
        $advancedinventoryStock = $connection->getTableName("advancedinventory_stock");
        $advancedinventoryItem = $connection->getTableName("advancedinventory_item");
        $cataloginventoryStockItem = $connection->getTableName("cataloginventory_stock_item");
        $advancedinventoryAssignation = $connection->getTableName("advancedinventory_assignation");
        $advancedinventoryIndex = $connection->getTableName("advancedinventory_index_flat");

        $manageStock = $this->_helperCore->getStoreConfig("cataloginventory/item_options/manage_stock");
        $minQty = $this->_helperCore->getStoreConfig("cataloginventory/item_options/min_qty");
        $backorders = $this->_helperCore->getStoreConfig("cataloginventory/item_options/backorders");

        $this->addAttributeToSelect('sku')
             ->addAttributeToSelect('name')
             ->addAttributeToSelect('attribute_set_id')
             ->addAttributeToSelect('type_id');
        if ($productId) {
            $this->addFieldToFilter("entity_id", ['eq' => $productId]);
        }

        $this->getSelect()
             ->joinLeft(
                 ["cataloginventory_stock_item" => $cataloginventoryStockItem], 'e.entity_id=cataloginventory_stock_item.product_id', [
                 "is_in_stock"    => "is_in_stock",
                 "qty"            => "qty",
                 "is_qty_decimal" => "is_qty_decimal",
             ], null, 'left'
             )
             ->joinLeft(
                 ['advancedinventory_item' => $advancedinventoryItem], 'e.entity_id=advancedinventory_item.product_id', [
                 "item_id"            => "id",
                 "multistock_enabled" => "multistock_enabled",
             ], null, 'left'
             );

        $on = null;
        if ($placeId) {
            $this->getSelect()->joinLeft(
                ['pointofsale' => $pointofsale], 'pointofsale.place_id =' . $placeId, [
                "default_stock_management"                  => "default_stock_management",
                "default_allow_backorder"                   => "default_allow_backorder",
                "default_use_default_setting_for_backorder" => "default_use_default_setting_for_backorder",
                "default_backorderable_at_stock_level"      => new \Zend_Db_Expr("IF(default_use_default_setting_for_backorder=1," . $backorders . ",IF(default_allow_backorder>0,1,0))"),
                "default_managed_at_stock_level"            => new \Zend_Db_Expr("IF((use_config_manage_stock=1 AND " . $manageStock . "=1) OR (use_config_manage_stock=0 AND cataloginventory_stock_item.manage_stock=1),default_stock_management,0)"),
            ], null, 'left'
            );
            $on = " AND advancedinventory_stock.place_id=pointofsale.place_id ";
        }

        $isError = true;
        if ($itemId && $placeId) {
            $this->getSelect()->joinLeft(['advancedinventory_assignation' => $advancedinventoryAssignation], 'advancedinventory_assignation.item_id="' . $itemId . '" AND advancedinventory_assignation.place_id="' . $placeId . '"', [], null, 'left');
            $this->getSelect()->joinLeft(
                ['advancedinventory_stock' => $advancedinventoryStock], 'e.entity_id=advancedinventory_stock.product_id ' . $on, [
                "quantity_in_stock" => new \Zend_Db_Expr("SUM(IF(advancedinventory_stock.manage_stock=1,advancedinventory_stock.quantity_in_stock+IFNULL(advancedinventory_assignation.qty_assigned,0),0))")
            ], null, 'left'
            );
            $isError = false;
        }
        if($isError) {
            $this->getSelect()->joinLeft(
                ['advancedinventory_stock' => $advancedinventoryStock], 'e.entity_id=advancedinventory_stock.product_id ' . $on, [
                "quantity_in_stock" => new \Zend_Db_Expr("SUM(IF(advancedinventory_stock.manage_stock=1,advancedinventory_stock.quantity_in_stock,0))")
            ], null, 'left'
            );
        }

        if (false) { //ICUBE CUSTOM - suspected bug when there are so many stores
            foreach ($placeIds as $s) {
                $this->getSelect()->joinLeft(
                    ['advancedinventory_stock_' . $s => $advancedinventoryStock], 'advancedinventory_stock_' . $s . ".item_id = advancedinventory_item.id AND advancedinventory_stock_" . $s . ".place_id=" . $s, [
                    "use_config_setting_for_backorders_$s" => "use_config_setting_for_backorders",
                    "backorder_allowed_$s"                 => "backorder_allowed",
                    "manage_stock_$s"                      => "manage_stock",
                    "quantity_$s"                          => new \Zend_Db_Expr("IF(advancedinventory_stock_$s.manage_stock=1,advancedinventory_stock_$s.quantity_in_stock,0)"),
                    "stock_id_$s"                          => "id",
                    "backorders_$s"                        => new \Zend_Db_Expr("IF(advancedinventory_stock_" . $s . ".use_config_setting_for_backorders=1," . $backorders . ",IF(advancedinventory_stock_" . $s . ".backorder_allowed>0,advancedinventory_stock_" . $s . ".backorder_allowed,0))"),
                    "is_in_stock_$s"                       => new \Zend_Db_Expr("IF(advancedinventory_stock_$s.manage_stock=1,IF(advancedinventory_stock_" . $s . ".quantity_in_stock>IF(use_config_min_qty=1," . $minQty . ",cataloginventory_stock_item.min_qty),1,0),1)"),
                ], null, 'left'
                );

                $this->getSelect()->joinLeft(
                    ['pointofsale_' . $s => $pointofsale], 'pointofsale_' . $s . '.place_id =' . $s, [
                    "default_stock_management_$s"                  => "default_stock_management",
                    "default_allow_backorder_$s"                   => "default_allow_backorder",
                    "default_use_default_setting_for_backorder_$s" => "default_use_default_setting_for_backorder",
                ], null, 'left'
                );
            }
        } else {
            $fields = [];
            foreach ($placeIds as $s) {
                $fields["use_config_setting_for_backorders_$s"] = "use_config_setting_for_backorders_$s";
                $fields["backorder_allowed_$s"] = "backorder_allowed_$s";
                $fields["manage_stock_$s"] = "manage_stock_$s";
                $fields["quantity_$s"] = "quantity_$s";
                $fields["stock_id_$s"] = "stock_id_$s";
                $fields["default_stock_management_$s"] = "default_stock_management_$s";
                $fields["default_allow_backorder_$s"] = "default_allow_backorder_$s";
                $fields["default_use_default_setting_for_backorder_$s"] = "default_use_default_setting_for_backorder_$s";
            }
            if (count($placeIds)) {
                $this->joinTable($advancedinventoryIndex, 'product_id=entity_id', $fields, null, 'left');
            }
        }
        $this->getSelect()->columns(
            [
                'backorderable_at_product_level' => new \Zend_Db_Expr("IF(use_config_backorders=1," . $backorders . ",backorders)"),
                'backorderable_at_stock_level'   => new \Zend_Db_Expr("MAX(IF(advancedinventory_stock.manage_stock=0,1,IF(advancedinventory_stock.use_config_setting_for_backorders=1," . $backorders . ",IF(advancedinventory_stock.backorder_allowed>0,advancedinventory_stock.backorder_allowed,0))))"),
                'managed_at_product_level'       => new \Zend_Db_Expr("IF(use_config_manage_stock=1,$manageStock,cataloginventory_stock_item.manage_stock )"),
                'managed_at_stock_level'         => new \Zend_Db_Expr("IF((use_config_manage_stock=1 AND " . $manageStock . "=1) OR (use_config_manage_stock=0 AND cataloginventory_stock_item.manage_stock=1),MIN(advancedinventory_stock.manage_stock),0)"),
                'min_qty'                        => new \Zend_Db_Expr("IF(use_config_min_qty=1," . $minQty . ",cataloginventory_stock_item.min_qty)"),
            ]
        )->group("e.entity_id")->limit(1);

        $data = $this->_resource->getConnection()->fetchAll($this->getSelect());
        if (!empty($data[0])) {
            $obj = new \Magento\Framework\DataObject($data[0]);
            return $obj;
        }
        return new \Magento\Framework\DataObject();
    }
}