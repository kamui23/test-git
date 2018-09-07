<?php

namespace Icube\Order\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_posFactory;
    protected $_stockFactory;
    protected $_cart;
    protected $_scopeConfig;

    public function __construct(
        \Wyomind\PointOfSale\Model\PointOfSaleFactory $posFactory,
        \Wyomind\AdvancedInventory\Model\ResourceModel\Stock\CollectionFactory $stockFactory,
        \Magento\Checkout\Model\Cart $cart,
        Context $context
    )
    {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_posFactory = $posFactory;
        $this->_stockFactory = $stockFactory;
        $this->_cart = $cart;
    }

    public function getStorelist($storeCode)
    {
        // $om = \Magento\Framework\App\ObjectManager::getInstance();
        $cartData = $this->_cart->getQuote()->getAllItems();
        $cartDataCount = count($cartData);
        $placeIds = array();
        if ($cartDataCount != 0) {
            $productIds = array();
            foreach ($cartData as $item) {
                $productIds[] = $item->getProductId();
                $stockCollection = $this->_stockFactory->create()
                                                       ->addFieldToFilter('main_table.product_id', ['eq' => $item->getProductId()])
                                                       ->addFieldToFilter('main_table.quantity_in_stock', ['gteq' => $item->getQty()]);
                foreach ($stockCollection as $stock) {
                    $placeIds[] = $stock->getPlaceId();
                }
            }

            $placeIds = array_unique($placeIds);
            $customerGroup = $this->_cart->getQuote()->getCustomerGroupId();
            $posCol = $this->_posFactory->create()->getCollection();

            if ($storeCode == 'ID') {
                $posCol->getSelect()
                       ->join(array('dcr' => 'directory_country_region'), 'dcr.code = main_table.state ', array('default_name',));
            }

            $posCol->addFieldToFilter('place_id', ['in' => $placeIds]);
            $posCol->addFieldToFilter('customer_group', ['like' => '%' . $customerGroup . '%']);
            $posCol->addFieldToFilter('status', ['eq' => 1]);
            $posCol->addFieldToFilter('country_code', ['eq' => $storeCode]);

            $places = array();
            foreach ($posCol as $pos) {
                $places[] = $pos->getData();
            }
            return $places;
        }

        //       $resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
        // $connection= $resources->getConnection();
        // $result = $connection->fetchAll("SELECT pos.*, dcr.default_name FROM pointofsale as pos 
        //           JOIN directory_country_region as dcr
        //           LEFT JOIN advancedinventory_stock as ais ON
        //           WHERE pos.state=dcr.code AND pos.state IS NOT NULL");
        //       return $result;
    }

    public function getStorelistPdp($id, $qty, $storeCode)
    {
        $placeIds = array();
        $stockCollection = $this->_stockFactory->create()
                                               ->addFieldToFilter('main_table.product_id', ['eq' => $id])
                                               ->addFieldToFilter('main_table.quantity_in_stock', ['gteq' => $qty]);
        foreach ($stockCollection as $stock) {
            $placeIds[] = $stock->getPlaceId();
        }
        $placeIds = array_unique($placeIds);
        $posCol = $this->_posFactory->create()->getCollection();

        if ($storeCode == 'ID') {
            $posCol->getSelect()
                   ->join(array('dcr' => 'directory_country_region'), 'dcr.code = main_table.state ', array('default_name',));
        }

        $posCol->addFieldToFilter('place_id', ['in' => $placeIds]);
        $posCol->addFieldToFilter('status', ['eq' => 1]);
        $posCol->addFieldToFilter('country_code', ['eq' => $storeCode]);

        $places = array();
        foreach ($posCol as $pos) {
            $places[] = $pos->getData();
        }

        return $places;

    }
}