<?php

namespace Kemana\AdvancedInventory\Plugin\Catalog\Block\Product\ProductList;

class Toolbar
{
    protected $_collectionNew = null;
    protected $_storeManager;
    protected $_customerSession;
    protected $_stockFilter;
    protected $_helper;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\CatalogInventory\Helper\Stock $stockFilter,
        \Kemana\AdvancedInventory\Helper\Data $helper)
    {
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_stockFilter = $stockFilter;
        $this->_helper = $helper;
    }

    public function afterGetCollection(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $collection)
    {
        // var_dump($collection->getData());die;
        // if (!$collection) {
        // 	$this->_collectionNew = 0;
        // } else {
        // 	$this->_collectionNew = count($collection->getData());

        // }
        return $collection;
    }

    // public function afterGetTotalNum(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $result) {
    // 	return $this->_collectionNew;
    // }

}