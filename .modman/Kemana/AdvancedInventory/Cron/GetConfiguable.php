<?php

namespace Kemana\AdvancedInventory\Cron;

use Kemana\AdvancedInventory\Model\AdvancedInventoryFactory as AdvancedInventoryFactory;

class GetConfiguable
{
    protected $_storeRepository;
    protected $_productCollection;
    protected $_advancedInventory;
    protected $_customerGroup;
    protected $_momaPos;
    protected $_stockRepo;
    protected $_stock;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Store\Model\StoreRepository $storeRepository,
        AdvancedInventoryFactory $advancedInventory,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,
        \Wyomind\PointOfSale\Model\PointOfSale $modelPos,
        \Wyomind\AdvancedInventory\Model\StockRepository $stockRepo,
        \Wyomind\AdvancedInventory\Model\Stock $stock)
    {
        $this->_storeRepository = $storeRepository;
        $this->_productCollection = $productCollection;
        $this->_advancedInventory = $advancedInventory;
        $this->_customerGroup = $customerGroup;
        $this->_momaPos = $modelPos;
        $this->_stockRepo = $stockRepo;
        $this->_stock = $stock;
    }

    public function execute()
    {
        $groupOptions = $this->_customerGroup->toOptionArray();
        $stores = $this->_storeRepository->getList();
        $collection = $this->_productCollection->create()->addAttributeToFilter('type_id', 'configurable');
        foreach ($stores as $store) {
            if ($store['store_id'] == 0) continue;
            foreach ($groupOptions as $group) {
                $customer_group_id = $group['value'];

                $placeidsbystorearr = $this->getPlacesbyStoreandCustomerGroup($store['store_id'], $customer_group_id);
                $productIds = array();
                foreach ($collection as $item) {
                    $_children = $item->getTypeInstance()->getUsedProducts($item);
                    foreach ($_children as $child) {
                        // $stock = $this->_stockRepo->getStockByProductId($child->getId(), $placeidsbystorearr);
                        // $stockArr = json_decode($stock, true);
                        foreach ($placeidsbystorearr as $value) {
                            $stockData = $this->_stock->getStockByProductIdAndPlaceId($child->getId(), $value);
                            if ($stockData['quantity_in_stock']) {
                                $productIds[] = $item->getId();
                                break;
                            }
                        }

                        if (in_array($item->getId(), $productIds)) break;
                    }
                }

                $advancedInventoryCollection = $this->_advancedInventory->create()->getCollection();

                $itemExist = $advancedInventoryCollection->addFieldToFilter('store_id', $store['store_id'])->addFieldToFilter('customer_group_id', $customer_group_id)->getFirstItem();
                if (count($itemExist->getData())) {
                    $itemExist->setData('content', json_encode($productIds));
                    $itemExist->save();
                } else {
                    $advancedInventoryObject = $this->_advancedInventory->create();
                    $advancedInventoryObject->setData('store_id', $store['store_id']);
                    $advancedInventoryObject->setData('customer_group_id', $customer_group_id);
                    $advancedInventoryObject->setData('content', json_encode($productIds));
                    $advancedInventoryObject->save();
                }
            }
        }
    }

    public function getPlacesbyStoreandCustomerGroup($storeId, $customerGroupId)
    {
        $collection = $this->_momaPos->getPlaces();
        // $collection->addFieldToFilter('status', ['status' => 1]);
        $arrData = $collection->setOrder('`position`', 'ASC')->getPlacesByStoreId($storeId, $customerGroupId)->getAllIds();
        return $arrData;

    }
}