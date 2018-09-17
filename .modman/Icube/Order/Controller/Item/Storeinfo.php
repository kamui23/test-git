<?php

namespace Icube\Order\Controller\Item;

class Storeinfo extends \Magento\Framework\App\Action\Action
{
    protected $_stockFactory  = null;
    protected $_posFactory    = null;
    protected $_cartFactory   = null;
    protected $_regionFactory = null;
    protected $resultJsonFactory;

    public function __construct(
        \Wyomind\AdvancedInventory\Model\ResourceModel\Stock\CollectionFactory $stockFactory,
        \Wyomind\PointOfSale\Model\PointOfSaleFactory $posFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Framework\App\Action\Context $context
    )
    {
        $this->_stockFactory = $stockFactory;
        $this->_posFactory = $posFactory;
        $this->_regionFactory = $regionFactory;
        $this->_cartFactory = $cartFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {

        $quote = $this->_cartFactory->create()->getQuote();
        if ($quote->getDeliveryPickup() == 'delivery') {
            return $this->resultJsonFactory->create()->setData(array('type' => 'delivery'));
        } else if ($quote->getDeliveryPickup() == 'pickup') {
            $storecode = $quote->getStoreCode();
            $pos = $this->_posFactory->create()->load($storecode, 'store_code');
            $region = $this->_regionFactory->create()->loadByCode($pos->getState(), $pos->getCountryCode());
            $data = array(
                'type'        => 'pickup',
                'store_name'  => $pos->getName(),
                'street1'     => $pos->getAddressLine1(),
                'street2'     => $pos->getAddressLine2(),
                'city'        => $pos->getCity(),
                'zip'         => $pos->getPostalCode(),
                'phone'       => $pos->getMainPhone(),
                'state'       => $pos->getState(),
                'longitude'   => $pos->getLongitude(),
                'latitude'    => $pos->getLatitude(),
                'store_code'  => $pos->getStoreCode(),
                'region_name' => $region->getName(),
                'region_id'   => $region->getRegionId()
            );
            return $this->resultJsonFactory->create()->setData($data);
        }
        $storecode = NULL;
        $arraySku = array();
        $arrayProdId = array();
        $cartData = $quote->getAllItems();
        foreach ($cartData as $item) {
            if ($item->getStoreCode() != NULL) {
                $storecode = $item->getStoreCode();
                $arraySku[] = $item->getSku();
                $arrayProdId[] = $item->getId();
            }
        }
        $pos = $this->_posFactory->create()->load($storecode, 'store_code');
        $region = $this->_regionFactory->create()->loadByCode($pos->getState(), $pos->getCountryCode());
        $data = array(
            'type'        => 'mixed',
            'pickup_sku'  => $arraySku,
            'pickup_id'   => $arrayProdId,
            'store_name'  => $pos->getName(),
            'street1'     => $pos->getAddressLine1(),
            'street2'     => $pos->getAddressLine2(),
            'city'        => $pos->getCity(),
            'zip'         => $pos->getPostalCode(),
            'phone'       => $pos->getMainPhone(),
            'state'       => $pos->getState(),
            'longitude'   => $pos->getLongitude(),
            'latitude'    => $pos->getLatitude(),
            'store_code'  => $pos->getStoreCode(),
            'region_name' => $region->getName(),
            'region_id'   => $region->getRegionId()
        );
        return $this->resultJsonFactory->create()->setData($data);
    }
}

?>