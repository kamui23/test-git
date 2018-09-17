<?php

namespace Kemana\Core\Plugin\Wyomind\PointOfSale\Controller\Adminhtml\Manage;

class Manage extends \Wyomind\AdvancedInventory\Plugin\PointOfSale\Controller\Adminhtml\Manage\Save
{
    public function afterExecute($subject, $return)
    {
        $journal = $this->_journalHelper;
        $placeId = $this->_requestInterface->getPost("place_id");

        if (!$placeId) {
            $placeId = $this->_posModel->getLastInsertedId();
        }

        try {
            if ($this->_requestInterface->getPost("posupdate") || ($this->_requestInterface->getPost('manage_inventory_backup') == 0 && $this->_requestInterface->getPost('manage_inventory') == 1)) {
                // update after pos update
                $defaultStockManagement = (int)$this->_requestInterface->getPost('default_stock_management');
                $defaultUseDefaultSettingForBackorder = (int)$this->_requestInterface->getPost('default_use_default_setting_for_backorder');
                $defaultAllowBackorder = (int)$this->_requestInterface->getPost('default_allow_backorder');
                $this->_itemModel->getCollection()->updateAfterPosUpdate($defaultStockManagement, $defaultUseDefaultSettingForBackorder, $defaultAllowBackorder, $placeId);
                $stocks = $this->_itemModel->getCollection();
                foreach ($stocks as $stock) {
                    $inventory = $this->_modelStockFactory->create()->getStockSettings($stock->getProductId());
                    if ($this->_coreHelper->getStoreConfig("advancedinventory/settings/auto_update_stock_status")) {
                        $is_in_stock = 0;
                        $backorders = 0;
                        $product_id = $stock->getProductId();
                        if ($is_in_stock) {
                            $is_in_stock = $inventory->getStockStatus();
                        }
                        if ($backorders) {
                            $backorders = $inventory->getBackorderableAtStockLevel();
                        }
                        $sql = "UPDATE cataloginventory_stock_item set is_in_stock=" . $is_in_stock . ", backorders=" . $backorders . ", use_config_backorders=0 where product_id=" . $product_id;
                        $this->_getWriteConnection()->query($sql);
                    }
                }
                $this->_journalHelper->insertRow($journal::SOURCE_POS, $journal::ACTION_MASS_UPDATE, "W#$placeId", ["from" => "Action", "to" => "Mass update pos/wh"]);
                $this->_messageManager->addSuccess(__("Stocks settings have been updated."));
            }

            if (!$this->_requestInterface->getPost('manage_inventory')) {
                $this->_posModel->setId($placeId)->setUseAssignationRules(0)->save();

                $stocks = $this->_stockCollectionFactory->create()->addFieldToFilter('place_id', ["eq" => $placeId]);
                foreach ($stocks as $stock) {
                    $stock->delete();
                    $inventory = $this->_modelStockFactory->create()->getStockSettings($stock->getProductId());

                    if ($this->_coreHelper->getStoreConfig("advancedinventory/settings/auto_update_stock_status")) {
                        $is_in_stock = 0;
                        $backorders = 0;
                        $product_id = $stock->getProductId();

                        if ($is_in_stock) {
                            $is_in_stock = $inventory->getStockStatus();
                        }
                        if ($backorders) {
                            $backorders = $inventory->getBackorderableAtStockLevel();
                        }
                        $sql = "UPDATE cataloginventory_stock_item set is_in_stock=" . $is_in_stock . ", backorders=" . $backorders . ", use_config_backorders=0 where product_id=" . $product_id;
                        $this->_getWriteConnection()->query($sql);
                    }
                }

                $this->_journalHelper->insertRow($journal::SOURCE_POS, $journal::ACTION_MASS_UPDATE, "W#$placeId", ["from" => "Action", "to" => "Mass disable stock management"]);
                $this->_messageManager->addSuccess(__('Inventory management disabled'));
            }
            if ($this->_modelStockFactory->create()->reindex()) {
                $this->_messageManager->addSuccess(__('Index updated `advancedinventory_index`'));
            }
        } catch (\Exception $e) {
            $this->_messageManager->addError(__('Error while updating data') . '<br/><br/>' . $e->getMessage());
        }
        return $return;
    }
}