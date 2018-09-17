<?php

namespace Kemana\AdvancedInventory\Plugin\Catalog\Model;

class Autocomplete
{
    protected $_momaPos;
    protected $_stock;
    protected $_storeManager;
    protected $_customer;
    protected $_helper;
    protected $_productRepository;
    protected $_objectManager;

    public function __construct(
        \Wyomind\PointOfSale\Model\PointOfSale $modelPos,
        \Wyomind\AdvancedInventory\Model\Stock $stock,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer $customer,
        \Kemana\AdvancedInventory\Helper\Data $helper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\ObjectManager $objectManager
    ){
        $this->_storeManager = $storeManager;
        $this->_momaPos = $modelPos;
        $this->_stock = $stock;
        $this->_customer = $customer;
        $this->_helper = $helper;
        $this->_productRepository = $productRepository;
        $this->_objectManager = $objectManager;
    }

    public function afterGetItems(\Magento\Search\Model\Autocomplete $subject, $result)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $storeCode = $this->_storeManager->getStore()->getCode();
        $session = 'customer_' . $storeCode . '_website';
        $groupId = $this->getGroupsId($this->_helper->isLoggedIn(), $session);

        foreach ($result as $key => $item) {
            $arrayData = $item->toArray();
            if ($arrayData['type'] == 'product') {
                $strPrice = $arrayData['price'];
                $arrPrice = explode(" ", $strPrice);
                $stringDataId = str_replace(' ', '', $arrPrice[4]);
                $arrDataId = explode("=", $stringDataId);
                $strid = str_replace('"', '', $arrDataId[1]);
                $productId = str_replace('>', '', $strid);
                $product = $this->_productRepository->getById($productId);
                $typeId = $product->getTypeId();
                $brandId = $product->getBrand();
                // echo $brandId.'---'.$productId;die;
                //Check brand enable
                $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
                $connection = $resource->getConnection();
                $sqlQuery = "SELECT a.id FROM icube_brands_items a
							JOIN kemana_brands_customer b ON a.id = b.ib_id
							JOIN kemana_brands_store c ON a.id = c.ib_id
							WHERE c.store_id = " . $storeId . " AND b.customer_group_id = " . $groupId . " AND a.attribute_id = " . $brandId;
                // echo $sqlQuery;die;
                $resulShow = $connection->fetchAll($sqlQuery);
                if (!count($resulShow)) {
                    unset($result[$key]);
                }
                //Check stock product
                if ($typeId == 'configurable') {
                    $arrayConfiguable = json_decode($this->_helper->getConfigurableProductInStock($storeId, $groupId));
                    if (!in_array((int)$productId, $arrayConfiguable)) {
                        unset($result[$key]);
                    }
                } elseif ($typeId == 'simple') {
                    $isStock = false;
                    $placeidsbystorearr = $this->getPlacesbyStoreandCustomerGroup($storeId, $groupId);
                    foreach ($placeidsbystorearr as $value) {
                        $stockData = $this->_stock->getStockByProductIdAndPlaceId($productId, $value);
                        if ($stockData['quantity_in_stock']) {
                            $isStock = true;
                        }
                    }
                    if (!$isStock) {
                        unset($result[$key]);
                    }

                }

            }
        }
        return $result;
    }

    protected function getGroupsId($isLoggedIn, $session) {
        if ($isLoggedIn) {
            $customer = $this->_customer->load($_SESSION[$session]['customer_id']);
            $groupId = $customer->getGroupId();
            return $groupId;
        }
        $groupId = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
        return $groupId;
    }

    public function getPlacesbyStoreandCustomerGroup($storeId, $customerGroupId)
    {
        $collection = $this->_momaPos->getPlaces();
        // $collection->addFieldToFilter('status', ['status' => 1]);
        $arrData = $collection->setOrder('`position`', 'ASC')->getPlacesByStoreId($storeId, $customerGroupId)->getAllIds();
        return $arrData;

    }

}