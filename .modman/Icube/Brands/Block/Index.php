<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Block;

class Index extends \Magento\Framework\View\Element\Template
{

    protected $_brandFactory;
    protected $_brand;
    protected $_customerSession;
    protected $productRule;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Icube\Brands\Model\ProductRuleProvider $productRule,
        \Icube\Brands\Model\BrandFactory $brandFactory,
        \Icube\Brands\Model\ResourceModel\Items $brand
    )
    {
        $this->productRule = $productRule;
        $this->_brandFactory = $brandFactory;
        $this->_brand = $brand;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    public function getGroupId()
    {
        if ($this->_customerSession->isLoggedIn()):
            $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
            return $customerGroupId;
        endif;

        return 0;
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getBrands()
    {
        $productList = $this->productRule->getRestrictedProductIds();
        // $list = $this->_brand->getActiveMatchesSelect();
        $collection = $this->_brandFactory->create()->getCollection()->getActiveCollection();
        $charBarndArray = array();
        foreach ($collection as $brand) {
            // print_r($brand->getName());
            // print_r($brand->getStoreId());
            // print_r($brand->getCustomerGroupId());
            // echo "<br>";
            $name = trim($brand->getName());
            $charBarndArray[strtoupper($name[0])][] = $brand;
        }

        // exit;

        return $charBarndArray;
    }

    public function getImageMediaPath()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        // return $this->getUrl('pub/media',['_secure' => $this->getRequest()->isSecure()]);
    }

    public function getFeaturedBrands()
    {
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $model = $objectManager->create(
        //     'Magento\Catalog\Model\ResourceModel\Eav\Attribute'
        // )->setEntityTypeId(
        //     \Magento\Catalog\Model\Product::ENTITY
        // );

        // $model->loadByCode(\Magento\Catalog\Model\Product::ENTITY,'manufacturer');
        // return $model->getOptions();

        $collection = $this->_brandFactory->create()->getCollection()->getActiveCollection();
        $collection->addFieldToFilter('featured', \Icube\Brands\Model\Status::STATUS_ENABLED);
        return $collection;
    }

}
