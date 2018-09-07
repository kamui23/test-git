<?php

namespace Kemana\AdvancedInventory\Plugin\Catalog\Model;

class Advanced
{
    protected $_helper;
    protected $_ruleProvider;

    public function __construct(
        \Kemana\AdvancedInventory\Helper\Data $helper,
        \Icube\Brands\Model\ProductRuleProvider $ruleProvider
    )
    {
        $this->_helper = $helper;
        $this->_ruleProvider = $ruleProvider;
    }

    public function afterAddFieldsToFilter(\Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Advanced\Collection $subject, $result)
    {
        $array = $this->_helper->getArrayProductId($result);
        if ($this->_helper->isEnable()) {
            $arrayBrand = $this->_ruleProvider->getProductIdsActive();
            $arrFinish = $this->getArrayFinish($arrayBrand, $array);
            $result->addFieldToFilter('entity_id', ['in' => $arrFinish]);
            return $result;
        }
        $result->addFieldToFilter('entity_id', ['in' => $array]);
        return $result;

    }

    protected function getArrayFinish($arrayBrand, $array) {
        if ($arrayBrand) {
            $farray = array_flip($array);
            $arrFinish = [];
            foreach ($arrayBrand as $v) {
                if (isset($farray[$v])) {
                    $arrFinish[] = $v;
                }
            }
            return $arrFinish;
        }
        $arrFinish = $array;
        return $arrFinish;
    }
}