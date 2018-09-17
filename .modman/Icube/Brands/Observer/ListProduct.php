<?php

namespace Icube\Brands\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class ListProduct implements ObserverInterface
{
    public function __construct(
        \Icube\Brands\Model\ProductRuleProvider $ruleProvider
    )
    {
        $this->ruleProvider = $ruleProvider;

    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // $productIds = $this->ruleProvider->getRestrictedProductIds();
        // // var_dump($productIds);die;
        // $newCollection = $observer->getEvent()
        //                         ->getCollection()
        //                         ->addAttributeToFilter('entity_id', ['nin' => $productIds]);
        //                         var_dump($newCollection->getAllIds());die;
        // return $this;
    }
}