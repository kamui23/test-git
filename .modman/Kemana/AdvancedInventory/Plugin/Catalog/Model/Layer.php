<?php

namespace Kemana\AdvancedInventory\Plugin\Catalog\Model;

class Layer
{

    protected $stockHelper;
    protected $_storeManager;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;
    protected $_state;
    protected $_helper;
    protected $_customer;
    protected $request;
    protected $ruleProvider;

    public function __construct(
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\State $state,
        \Kemana\AdvancedInventory\Helper\Data $helper,
        \Magento\Customer\Model\Customer $customer,
        \Icube\Brands\Model\ProductRuleProvider $ruleProvider,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->stockHelper = $stockHelper;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->_registry = $registry;
        $this->_state = $state;
        $this->_helper = $helper;
        $this->_customer = $customer;
        $this->ruleProvider = $ruleProvider;
        $this->request = $request;
    }

    public function beforePrepareProductCollection(\Magento\Catalog\Model\Layer $subject, $collection)
    {
        $category = $this->_registry->registry('current_category');
        $q = $this->request->getParam('q');
        // var_dump($collection->getData());die;
        if ($category || isset($q)) {
            $array = $this->_helper->getArrayProductId($collection);
            if ($this->_helper->isEnable()) {
                $arrayBrand = $this->ruleProvider->getProductIdsActive();
                $farray = array_flip($array);
                $arrFinish = [];
                foreach ($arrayBrand as $v) {
                    if (isset($farray[$v])) {
                        $arrFinish[] = $v;
                    }
                }
                $collection->addFieldToFilter('entity_id', ['in' => $arrFinish]);
            } else {
                $collection->addFieldToFilter('entity_id', ['in' => $array]);
            }
        }
        return [$collection];
    }

    // public function afterGetProductCollection(\Magento\Catalog\Model\Layer $subject,$result){
    // 	$category = $this->_registry->registry('current_category');
    // 	$q = $this->request->getParam('q');
    // 	if ($category || isset($q)) {
    // 		$array = $this->_helper->getArrayProductId($result);
    // 		if ($this->_helper->isEnable()) {
    // 			$arrayBrand = $this->ruleProvider->getProductIdsActive();
    // 			// var_dump($arrayBrand);die;
    // 			$farray = array_flip($array);
    // 			$arrFinish = [];
    // 			foreach ($arrayBrand as $v) {
    // 				if (isset($farray[$v])) {
    // 					$arrFinish[] = $v;
    // 				}

    // 			}
    // 			$result->addFieldToFilter('entity_id', ['in' => $arrFinish]);
    // 		} else {
    // 			$result->addFieldToFilter('entity_id', ['in' => $array]);
    // 		}

    // 	}
    // 	return $result;
    // }

}
