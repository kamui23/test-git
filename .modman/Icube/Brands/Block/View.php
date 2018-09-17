<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Block;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\ObjectManager;

class View extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Image helper
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;

    protected $_brandFactory;
    protected $_objectManager;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Icube\Brands\Model\BrandFactory $brandFactory,
        ObjectManager $objectManager,
        array $data = []
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->_imageHelper = $context->getImageHelper();
        $this->_brandFactory = $brandFactory;
        $this->_cartHelper = $context->getCartHelper();
        $this->_objectManager = $objectManager;
        parent::__construct(
            $context,
            $data
        );
        $this->setCollection($this->getProductCollection());
    }

    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->_cartHelper->getAddUrl($product, $additional);
    }


    public function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'brand.view.pager'
        );
        $pager->setLimit(12)
              ->setShowAmounts(false)
              ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();

        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getBrand()
    {
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $model = $objectManager->create(
        //     'Magento\Catalog\Model\ResourceModel\Eav\Attribute'
        // )->setEntityTypeId(
        //     \Magento\Catalog\Model\Product::ENTITY
        // );

        // $model->loadByCode(\Magento\Catalog\Model\Product::ENTITY,'manufacturer');
        // return $model->getOptions();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = $this->_objectManager->create('Icube\Brands\Model\Items');
            $model->load($id);
            return $model;
        }
        return false;
    }

    public function getProductCollection()
    {
        $brand = $this->getBrand();
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
//     	var_dump(get_class_methods($collection));
//     	die;
        $collection->addAttributeToSelect('name');
        $scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $scopeConfig->getValue(
            'icube_brands/config/attribute_name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $collection->addStoreFilter()->addAttributeToFilter($value, $brand->getAttributeId());

        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        $collection->addAttributeToFilter('visibility', array('neq' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE));


//     	var_dump(count($collection));
        return $collection;
    }

    public function imageHelperObj()
    {
        return $this->_imageHelper;
    }

    public function getProductPricetoHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null
    )
    {
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product
            );
        }
        return $price;
    }
}
