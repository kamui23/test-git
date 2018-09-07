<?php

namespace Icube\Order\Model\Rewrite\Carrier;

class Advancerate extends \Ced\Advancerate\Model\ResourceModel\Carrier\Advancerate
{
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $coreConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ced\Advancerate\Model\Carrier\Advancerate $carrierTablerate,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        Filesystem $filesystem,
        \Magento\Catalog\Model\Session $citySession,
        \Magento\Framework\App\ObjectManager $objectManager,
        $connectionName = null
    ) {
        parent::__construct($context, $logger, $coreConfig, $storeManager, $carrierTablerate, $countryCollectionFactory, $regionCollectionFactory, $filesystem, $citySession, $connectionName);
        $this->_objectManager = $objectManager;
    }


    public function getRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $connection = $this->getConnection();
        $condition = $this->_coreConfig->getValue('carriers/advancerate/ratecondition', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $postcode = $request->getDestPostcode();
        $city = $request->getDestCity();

        $isError = true;
        if (strlen($postcode) > 6) {
            $data = explode('/', $postcode);
            $ndata = count($data);
            if ($ndata == 3) {
                $postcode = $data[0];
                $city = $data[1] . '/' . $data[2];
            } elseif ($ndata == 4) {
                $postcode = $data[0];
                $city = $data[1] . '/' . $data[2] . '/' . $data[3];
            }
            $isError = false;
        }
        if($isError) {
            $postcode = $request->getDestPostcode();
            $city = $request->getDestCity();
            if (!$city) {
                $city = $this->_citySession->getMyValue();
            }
        }


        $bind = [
            ':website_id'      => (int)$request->getWebsiteId(),
            //':vendor_id' => $this->getVendorId(),
            ':dest_country_id' => $request->getDestCountryId(),
            ':dest_region_id'  => (int)$request->getDestRegionId(),
            ':city'            => $city,
            ':dest_zip'        => $postcode
        ];
        $select = $connection->select()->from(
            $this->getMainTable()
        )->where(
            'website_id = :website_id'
        )/*->where(
	            'vendor_id= :vendor_id'
	       )*/
        ;


        switch ($condition) {
            case 0:
                $bind[':weight'] = $request->getPackageWeight();

                $select->where('weight_from <= :weight')
                       ->where('weight_to >= :weight')
                       ->order(array('dest_country_id DESC', 'dest_region_id DESC', 'city DESC', 'dest_zip DESC'));

                break;

            case 1:
                $bind[':order_total'] = $request->getPackageValue();

                $select->where('price_from <= :order_total')
                       ->where('price_to >= :order_total')
                       ->order(array('dest_country_id DESC', 'dest_region_id DESC', 'city DESC', 'dest_zip DESC'))->limit(1);

                break;

            case 2:
                $bind[':qty'] = $request->getPackageQty();

                $select->where('qty_from <= :qty')
                       ->where('qty_to >= :qty')
                       ->order(array('dest_country_id DESC', 'dest_region_id DESC', 'city DESC', 'dest_zip DESC'));
                break;
        }

        $orWhere = '(' . implode(') OR (', array(
                "dest_country_id = :dest_country_id AND dest_region_id = :dest_region_id AND city = :city AND dest_zip = :dest_zip",
                "dest_country_id = :dest_country_id AND dest_region_id = :dest_region_id AND city = '*'   AND dest_zip = :dest_zip",
                "dest_country_id = :dest_country_id AND dest_region_id = 0       AND city = :city AND dest_zip = :dest_zip",
                "dest_country_id = :dest_country_id AND dest_region_id = 0       AND city = '*'   AND dest_zip = :dest_zip",

                "dest_country_id = '0' AND dest_region_id = :dest_region_id AND city = :city AND dest_zip = :dest_zip",
                "dest_country_id = '0' AND dest_region_id = :dest_region_id AND city = '*'   AND dest_zip = :dest_zip",
                "dest_country_id = '0' AND dest_region_id = 0 AND city = :city AND dest_zip = :dest_zip",
                "dest_country_id = '0' AND dest_region_id = 0 AND city = '*' AND dest_zip = :dest_zip",

                "dest_country_id = :dest_country_id AND dest_region_id = :dest_region_id AND city = :city AND dest_zip = '*'",
                "dest_country_id = :dest_country_id AND dest_region_id = :dest_region_id AND city = '*'   AND dest_zip = '*'",
                "dest_country_id = :dest_country_id AND dest_region_id = 0       AND city = :city AND dest_zip = '*'",
                "dest_country_id = :dest_country_id AND dest_region_id = 0       AND city = '*'   AND dest_zip = '*'",

                "dest_country_id = '0' AND dest_region_id = :dest_region_id AND city = :city AND dest_zip = '*'",
                "dest_country_id = '0' AND dest_region_id = :dest_region_id AND city = '*'   AND dest_zip = '*'",
                "dest_country_id = '0' AND dest_region_id = 0 AND city = :city AND dest_zip = '*'",
                "dest_country_id = '0' AND dest_region_id = 0 AND city = '*' AND dest_zip = '*'",

            )) . ')';


        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/resulte.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        //$logger->info(print_r($bind, true));

        $select->where($orWhere);

        $result = $connection->fetchAll($select, $bind);
        //$logger->info(print_r($result, true));

        $methods = array();
        $rates = array();
        $weight_type = $this->_coreConfig->getValue('carriers/advancerate/weight_type', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $shippingWeight = $this->getShippingWeight($weight_type, $request);

        $dimensional_condition = $this->_coreConfig->getValue('carriers/advancerate/dimensional_calculation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);


        if (!empty($result)) {
            foreach ($result as $key => $value) {
                if (!in_array($value['shipping_method'], $methods)) {
                    $items = $request->getAllItems();
                    $rate = $value['price'];
                    $totalPrice = 0;
                    $totalWeight = 0;
                    $isError = true;
                    if ($dimensional_condition == 1) {
                        foreach ($items as $item) {
                            $product = $this->_objectManager->create('Magento\Catalog\Model\Product')
                                                     ->load($item->getProductId());
                            //product dimension in cm
                            $height = $product->getData('dimension_package_height');
                            $length = $product->getData('dimension_package_length');
                            $width = $product->getData('dimension_package_width');
                            $weight = $product->getData('weight');
                            $qty = $item->getQty();

                            $totalWeight = $weight * $qty;
                            if ($weight_type == 1) {
                                $totalWeight = $totalWeight / 1000;
                            }

                            $totalVolume = ($length * $width * $height) * $qty;

                            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/jne.log');
                            $logger = new \Zend\Log\Logger();
                            $logger->addWriter($writer);
                            $logger->info("volume :" . ($totalVolume / 6000) . " weight:" . $totalWeight . " weighttype:" . $weight_type);

                            ##dibandingkan berat dg volume/6000##
                            $max = max($totalWeight, ($totalVolume / 6000));
                            // $max = $max + ($max*1/10);
                            $price = $this->round_up($max, 0) * $rate;
                            $totalPrice += $price;

                        }
                        $isError = false;
                    }
                    if($isError) {
                        $totalPrice = $rate * $shippingWeight;
                        $totalWeight = $request->getPackageWeight();
                        $totalPickupWeight = 0;
                        foreach ($items as $item) {
                            if (!empty($item->getStoreCode())) {
                                $totalPickupWeight += $item->getWeight() * $item->getQty();
                            }
                        }
                        if ($totalPickupWeight > 0) {
                            $totalPrice = $rate * ($totalWeight - $totalPickupWeight);
                        }
                    }

                    $rates[] = array(
                        'method' => $value['shipping_method'],
                        'label'  => $value['shipping_label'],
                        'price'  => $totalPrice,
                        'etd'    => $value['etd']
                    );
                }
            }
        }
        return $rates;
    }

    protected function getShippingWeight($weight_type, $request) {
        if ($weight_type == 1) { //0 kilogram , 1 gram
            $shippingWeight = ceil($request->getPackageWeight() / 1000);
            return $shippingWeight;
        }
        $shippingWeight = ceil($request->getPackageWeight());
        return $shippingWeight;
    }

}