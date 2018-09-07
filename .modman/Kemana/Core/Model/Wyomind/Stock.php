<?php

namespace Kemana\Core\Model\Wyomind;

class Stock extends \Wyomind\AdvancedInventory\Model\Stock
{
    public function reindex()
    {
        $advancedinventoryStock = $this->_resourceConnection->getTableName("advancedinventory_stock");
        $pointofsale = $this->_resourceConnection->getTableName("pointofsale");
        $advancedinventoryItem = $this->_resourceConnection->getTableName("advancedinventory_item");

        $fields = [];
        $sqldelete = "drop table if exists advancedinventory_index_flat";
        $sql = "CREATE TABLE advancedinventory_index_flat AS ( SELECT product_id,";
        $pos = $this->_posFactory->create()->getCollection();
        foreach ($pos as $p) {
            $fields[] = "(SELECT quantity_in_stock FROM $advancedinventoryStock WHERE place_id=" . $p->getPlaceId() . " AND item_id=$advancedinventoryItem.id) AS quantity_" . $p->getPlaceId() . "";
            $fields[] = "(SELECT manage_stock FROM $advancedinventoryStock WHERE place_id=" . $p->getPlaceId() . " AND item_id=$advancedinventoryItem.id) AS manage_stock_" . $p->getPlaceId() . "";
            $fields[] = "(SELECT backorder_allowed FROM $advancedinventoryStock WHERE place_id=" . $p->getPlaceId() . " AND item_id=$advancedinventoryItem.id) AS backorder_allowed_" . $p->getPlaceId() . "";
            $fields[] = "(SELECT use_config_setting_for_backorders FROM $advancedinventoryStock WHERE place_id=" . $p->getPlaceId() . " AND item_id=$advancedinventoryItem.id) AS use_config_setting_for_backorders_" . $p->getPlaceId() . "";
            $fields[] = "(SELECT id FROM $advancedinventoryStock WHERE place_id=" . $p->getPlaceId() . " AND item_id=$advancedinventoryItem.id) AS stock_id_" . $p->getPlaceId() . "";

            $fields[] = "(SELECT default_stock_management FROM $pointofsale WHERE place_id=" . $p->getPlaceId() . " ) AS default_stock_management_" . $p->getPlaceId() . "";
            $fields[] = "(SELECT default_use_default_setting_for_backorder FROM $pointofsale WHERE place_id=" . $p->getPlaceId() . " ) AS default_use_default_setting_for_backorder_" . $p->getPlaceId() . "";
            $fields[] = "(SELECT default_allow_backorder FROM $pointofsale WHERE place_id=" . $p->getPlaceId() . " ) AS default_allow_backorder_" . $p->getPlaceId() . "";
        }
        $sql .= implode(",", $fields);
        $sql .= " FROM $advancedinventoryItem GROUP BY id )";

        if (count($pos) && $this->_getWriteConnection()->query($sqldelete)) {
            if (count($pos) && $this->_getWriteConnection()->query($sql)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}