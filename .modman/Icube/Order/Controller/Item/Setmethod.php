<?php
namespace Icube\Order\Controller\Item;

class Setmethod extends \Magento\Framework\App\Action\Action
{
    protected $_stockFactory = null;
    protected $_posFactory   = null;
    protected $resultJsonFactory;
    protected $resourceCon;
    protected $_cart;

    public function __construct(
        \Wyomind\AdvancedInventory\Model\ResourceModel\Stock\CollectionFactory $stockFactory,
        \Wyomind\PointOfSale\Model\PointOfSaleFactory $posFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\ResourceConnection $resourceCon,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Cart $cart
    )
    {
        $this->_stockFactory = $stockFactory;
        $this->_posFactory = $posFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resourceCon = $resourceCon;
        $this->_cart = $cart;
        parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $params = $this->getRequest()->getParams();
        $placeid = array_key_exists('place_id', $params) ? $params['place_id'] : NULL;
        $pos = $this->_posFactory->create()->load($placeid);
        $storecode = $pos->getStoreCode();

        $quote = $this->_cart->getQuote();
        if ($params['method'] == 'delivery') {
            $quote->setDeliveryPickup('delivery')->setStoreCode(NULL)->save();
            $cartData = $quote->getAllItems();
            $arrayOos = $this->validateDeliveryStock($cartData); //check warehouse stock, oos = out of stock
            if (count($arrayOos['sku']) == 0) {
                return $this->resultJsonFactory->create()->setData(json_encode(array('type' => 'delivery')));
            }
            $quote->setDeliveryPickup('oos')->setStoreCode(NULL)->save();
            return $this->resultJsonFactory->create()->setData(array('message' => $arrayOos['message'], 'sku' => $arrayOos['sku'], 'type' => 'oos'));
        }
        $cartData = $quote->getAllItems();
        $cartDataCount = count($cartData);
        // $placeIds = array();
        if ($cartDataCount != 0) {
            $productIds = array();
            // $result = 1;
            $arraySku = array();
            $message = array();
            $parentId = array();
            $parentSku = array();
            $qtyTotal = array();
            $pickupCount = 0;
            $deliveryCount = 0;
            // $quote->setDeliveryPickup('pickup')->save();
            $connection = $this->resourceCon->getConnection();
            foreach ($cartData as $item) {
                $isParentConfigurable = false;
                $parent = null;
                if ($item->getParentItemId() != NULL) {
                    $sql = "Select `product_type`,`qty` FROM quote_item WHERE item_id =" . $item->getParentItemId();
                    $result = $connection->fetchAll($sql);
                    $isParentConfigurable = $result[0]['product_type'];
                }

                $notExistKeys = true;
                if (array_key_exists($item->getProductId(), $qtyTotal)) {
                    if ($isParentConfigurable)
                        $qtyTotal[$item->getProductId()] += $result[0]['qty'];
                    else
                        $qtyTotal[$item->getProductId()] += $item->getQty();
                    $notExistKeys = false;
                }
                if($notExistKeys) {
                    if ($isParentConfigurable)
                        $qtyTotal[$item->getProductId()] = $result[0]['qty'];
                    else
                        $qtyTotal[$item->getProductId()] = $item->getQty();
                }
                // $productIds[] = $item->getProductId();
                if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                    $stockCollection = $this->_stockFactory->create()
                                                           ->addFieldToFilter('main_table.place_id', ['eq' => $placeid])
                                                           ->addFieldToFilter('main_table.quantity_in_stock', ['gteq' => $qtyTotal[$item->getProductId()]])
                                                           ->addFieldToFilter('main_table.product_id', ['eq' => $item->getProductId()]);
                    $haveStock = true;
                    if (count($stockCollection) == 0) {
                        // $result = 0;
                        $deliveryCount++;
                        $haveParentId = true;
                        if ($item->getParentItemId() == NULL) {
                            $sku = $item->getSku();
                            $arraySku[] = $sku;
                            $message[$sku] = "This product does not have enough stock you requested in this store and will be delivered instead";
                            $haveParentId = false;
                        }
                        if($haveParentId) {
                            $parentId[] = $item->getParentItemId();
                        }
                        $haveStock = false;
                    }
                    if($haveStock) {
                        $pickupCount++;
                    }
                } else if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE || $item->getProductType() == 'configurable') {
                    $parentSku[$item->getId()] = $item->getSku();
                }
            }

            //filtering bundle/configurable child not enough stock message
            foreach ($parentId as $id) {
                $arraySku[] = $parentSku[$id];
                $message[$parentSku[$id]] = "This product does not have enough stock you requested in this store and will be delivered instead";
            }

            //condition 1: all items and their qtys are enough for pickup
            // original ES code: if ($result == 1){
            if ($deliveryCount == 0 && $pickupCount > 0) {
                $quote->setDeliveryPickup('pickup')->setStoreCode($storecode)->save();
                foreach ($cartData as $item) {
                    $item->setStoreCode($storecode)->save();
                }
                return $this->resultJsonFactory->create()->setData(array('type' => 'pickup'));
            }
            //condition 2: mixed between pickup and delivery / not all items are available for pickup
            // original ES code: if ($result == 0){
            else if ($deliveryCount > 0 && $pickupCount > 0) {
                $quote->setDeliveryPickup('mixed')->setStoreCode($storecode)->save();
                $toDeliveryItems = array();
                foreach ($cartData as $item) {
                    $notInArrSku = true;
                    if (in_array($item->getSku(), $arraySku)) {
                        $toDeliveryItems[] = $item; //to be validated in validateDeliveryStock function
                        $notInArrSku = false;
                    }
                    if($notInArrSku) {
                        $item->setStoreCode($storecode)->save();
                    }
                }
                $arrayOos = $this->validateDeliveryStock($toDeliveryItems); //check warehouse stock, oos = out of stock
                if (count($arrayOos['sku']) == 0) {
                    return $this->resultJsonFactory->create()->setData(array('message' => $message, 'sku' => $arraySku, 'type' => 'mixed'));
                }
                $quote->setDeliveryPickup('oos')->setStoreCode(NULL)->save();
                return $this->resultJsonFactory->create()->setData(array('message' => $arrayOos['message'], 'sku' => $arrayOos['sku'], 'type' => 'oos'));
            } //condition 3: all items are not available for pickup

            $quote->setDeliveryPickup('delivery')->setStoreCode(NULL)->save();
            $arrayOos = $this->validateDeliveryStock($cartData); //check warehouse stock, oos = out of stock
            if (count($arrayOos['sku']) == 0) {
                return $this->resultJsonFactory->create()->setData(json_encode(array('type' => 'delivery')));
            }
            $quote->setDeliveryPickup('oos')->setStoreCode(NULL)->save();
            return $this->resultJsonFactory->create()->setData(array('message' => $arrayOos['message'], 'sku' => $arrayOos['sku'], 'type' => 'oos'));
        }
    }

    public function validateDeliveryStock($items)
    {
        $arraySku = array();
        $message = array();
        $parentId = array();
        $parentSku = array();
        $qtyTotal = array();
        $type = NULL;
        $connection = $this->resourceCon->getConnection();
        foreach ($items as $item) {
            $item->setStoreCode(NULL)->save(); //set item store code to null

            //if parent configurable, get qty from configurable parent. child configurable doesn't hold the actual qty.
            if ($item->getParentItemId() != NULL) {
                $sql = "Select `product_type`,`qty` FROM quote_item WHERE item_id =" . $item->getParentItemId();
                $result = $connection->fetchAll($sql);
                $type = $result[0]['product_type'];
            }
            $notExistKeys = true;
            if (array_key_exists($item->getProductId(), $qtyTotal)) { //checking if the array key is exists to sum the qty
                if ($type == 'configurable')
                    $qtyTotal[$item->getProductId()] += $result[0]['qty'];
                else
                    $qtyTotal[$item->getProductId()] += $item->getQty();
                $notExistKeys = false;
            }
            if($notExistKeys) {
                if ($type == 'configurable')
                    $qtyTotal[$item->getProductId()] = $result[0]['qty'];
                else
                    $qtyTotal[$item->getProductId()] = $item->getQty();
            }

            //query to advancedinventory_stock for stocks in warehouses
            if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                $stockCollection = $this->_stockFactory->create();
                $stockCollection->getSelect()
                                ->joinLeft(array('p' => 'pointofsale'), 'p.place_id = main_table.place_id', array('p.status'));
                $stockCollection->addFieldToFilter('main_table.quantity_in_stock', ['gteq' => $qtyTotal[$item->getProductId()]])
                                ->addFieldToFilter('main_table.product_id', ['eq' => $item->getProductId()])
                                ->addFieldToFilter('p.status', ['eq' => 0]);
                if (count($stockCollection) == 0) {
                    // $result = 0;
                    $haveParentId = true;
                    if ($item->getParentItemId() == NULL) {
                        $sku = $item->getSku();
                        $arraySku[] = $sku;
                        $message[$sku] = "The product that you requested does not have enough stock";
                        $haveParentId = false;
                    }
                    if($haveParentId) {
                        $parentId[] = $item->getParentItemId();
                    }
                }
            } else if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE || $item->getProductType() == 'configurable') {
                $parentSku[$item->getId()] = $item->getSku();
            }

            //filtering bundle/configurable child not enough stock message
            foreach ($parentId as $id) {
                $arraySku[] = $parentSku[$id];
                $message[$parentSku[$id]] = "This product that you requested does not have enough stock";
            }
        }

        $arrayOos = array();
        $arrayOos['sku'] = $arraySku;
        $arrayOos['message'] = $message;
        return $arrayOos;
    }
}

?>
