<?php

namespace Icube\Brands\Plugin\Catalog\Block\Product\ProductList;

class Toolbar
{
    protected $_objectManager;
    protected $request;
    protected $ruleProvider;

    /**
     * Toolbar constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param \Icube\Brands\Model\ProductRuleProvider $ruleProvider
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Icube\Brands\Model\ProductRuleProvider $ruleProvider,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->ruleProvider = $ruleProvider;
        $this->_objectManager = $objectmanager;
        $this->request = $request;
    }

    protected $_collectionNew = null;

    public function afterGetCollection(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $collection)
    {
        // var_dump($collection->getData());die;
        // if (!$collection) {
        //   $this->_collectionNew = 0;
        // } else {
        //   $this->_collectionNew = count($collection->getData());

        // }
        return $collection;
    }
    // public function afterGetTotalNum(\Magento\Catalog\Block\Product\ProductList\Toolbar $subject, $result) {
    //   return $this->_collectionNew;
    // }
}