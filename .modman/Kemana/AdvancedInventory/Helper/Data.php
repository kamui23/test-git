<?php

namespace Kemana\AdvancedInventory\Helper;

use Kemana\AdvancedInventory\Model\AdvancedInventoryFactory as AdvancedInventoryFactory;
use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_productCollectionFactory;
    protected $_stockRepo;
    protected $_storeRepo;
    protected $_advancedInventory;
    protected $_storeManager;
    protected $_customer;
    protected $_httpContext;
    protected $moduleManager;

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productCollectionFactory,
        \Wyomind\AdvancedInventory\Model\StockRepository $stockRepo,
        \Magento\Store\Model\StoreRepository $storeRepo,
        AdvancedInventoryFactory $advancedInventory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer $customer, \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Http\Context $httpContext
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_stockRepo = $stockRepo;
        $this->_storeRepo = $storeRepo;
        $this->_advancedInventory = $advancedInventory;
        $this->_storeManager = $storeManager;
        $this->_customer = $customer;
        $this->moduleManager = $moduleManager;
        $this->_httpContext = $httpContext;
    }


    public function isLoggedIn()
    {
        return $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function getConfigurableProductInStock($storeId, $groupId)
    {
        $advancedInventoryCollection = $this->_advancedInventory->create()->getCollection();
        $itemExist = $advancedInventoryCollection->addFieldToFilter('store_id', $storeId)->addFieldToFilter('customer_group_id', $groupId)->getFirstItem()->getData();
        if (isset($itemExist['content'])) {
            return $itemExist['content'];
        }

        return null;
    }

    public function isEnable()
    {
        return $this->moduleManager->isOutputEnabled('Icube_Brands');
    }

    public function getArrayProductId($collection)
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $storeCode = $this->_storeManager->getStore()->getCode();

        $session = 'customer_' . $storeCode . '_website';
        $isLoggedIn = $this->isLoggedIn();
        $groupId = $this->getGroupsId($isLoggedIn, $session);

        $arrayConfiguable = json_decode($this->getConfigurableProductInStock($storeId, $groupId));

        $idField = 'e.entity_id';

        $collection->getSelect()
                   ->joinLeft(
                       ["advancedinventory_stock"],
                       "advancedinventory_stock.product_id=$idField",
                       [
                           "qtyWh" => "SUM(advancedinventory_stock.quantity_in_stock )",
                       ]
                   )->joinLeft(["pointofsale"], "advancedinventory_stock.place_id = pointofsale.place_id", []);
        $collection->getSelect()->where('pointofsale.country_code = "' . strtoupper($storeCode) . '" AND FIND_IN_SET(' . $groupId . ', pointofsale.customer_group )');

        $havingCond = $this->getHavingCond($arrayConfiguable);
        $collection->getSelect()
            ->group($idField)
            ->having($havingCond);

        $ids = $collection->getAllIds();
        return $ids;
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

    protected function getHavingCond($arrayConfiguable) {
        if ($arrayConfiguable) {
            $arrayConfiguableStr = implode(',', $arrayConfiguable);
            return 'SUM(advancedinventory_stock.quantity_in_stock ) > 0 OR e.entity_id IN (' . $arrayConfiguableStr . ')';
        }
        return 'SUM(advancedinventory_stock.quantity_in_stock ) > 0';
    }

}