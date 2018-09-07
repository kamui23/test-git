<?php

namespace Icube\Brands\Plugin\Catalog\Model\ResourceModel\Product;

use Magento\Catalog\Model\Product;

class Collection
{
    // /**
    //  * @var \Icube\Brands\Model\ProductRuleProvider
    //  */
    // private $ruleProvider;

    // /**
    //  * @var \Magento\Framework\Registry
    //  */
    // private $coreRegistry;

    // /**
    //  * Collection constructor.
    //  *
    //  * @param \Icube\Brands\Model\ProductRuleProvider $ruleProvider
    //  * @param \Magento\Framework\Registry                $coreRegistry
    //  */
    // public function __construct(
    //     \Icube\Brands\Model\ProductRuleProvider $ruleProvider,
    //     \Magento\Framework\Registry $coreRegistry
    // ) {
    //     $this->ruleProvider = $ruleProvider;
    //     $this->coreRegistry = $coreRegistry;
    // }

    // /**
    //  * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
    //  * @param                                                         $printQuery
    //  * @param                                                         $logQuery
    //  * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    //  */
    // public function beforeLoad(
    //     \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
    //     $printQuery = null,
    //     $logQuery = null
    // ) {
    //     // echo "<pre>";
    //     // print_r($subject->getFlag('brands_filter_applied'));
    //     // exit;
    //     // if ($subject->getFlag('brands_filter_applied')
    //     //     || $subject->isLoaded()
    //     // ) {
    //     //     return;
    //     // }
    //     $this->addRestrictedProductFilter($subject, $subject->getSelect());
    // }

    // /**
    //  * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
    //  * @param \Magento\Framework\DB\Select                            $productSelect
    //  *
    //  * @return \Magento\Framework\DB\Select
    //  * @SuppressWarnings(PHPMD.UnusedFormalParameter)
    //  */
    // public function afterGetSelect(
    //     \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
    //     \Magento\Framework\DB\Select $productSelect
    // ) {
    //     // echo "<pre>";
    //     // print_r($subject->getFlag('groupcat_filter_applied'));
    //     // exit;
    //     // if ($subject->getFlag('groupcat_filter_applied')
    //     //     || !count($productSelect->getPart($productSelect::FROM)) //avoid _initSelect
    //     //     || $this->coreRegistry->registry('amasty_ignore_product_filter')
    //     // ) {
    //     //     return $productSelect;
    //     // }
    //     // $this->addRestrictedProductFilter($subject, $productSelect);

    //     return $productSelect;
    // }

    // /**
    //  * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
    //  */
    // public function beforeGetSize(\Magento\Catalog\Model\ResourceModel\Product\Collection $subject)
    // {
    //     $this->addRestrictedProductFilter($subject, $subject->getSelect());
    // }

    // /**
    //  * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
    //  * @param \Magento\Framework\DB\Select                            $productSelect
    //  */
    // protected function addRestrictedProductFilter(
    //     \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
    //     \Magento\Framework\DB\Select $productSelect
    // ) {
    //     // echo "<pre>";
    //     // print_r('this');
    //     // exit;
    //     $productIds = $this->ruleProvider->getRestrictedProductIds();
    //     // echo "<pre>";
    //     // print_r($productIds);
    //     // exit;

    //     if ($productIds && $subject->getIdFieldName() == 'entity_id') {
    //         $idField = $subject::MAIN_TABLE_ALIAS . '.entity_id';
    //         $productSelect->where($idField . ' NOT IN (?)', $productIds);
    //     }       
    // }
}
