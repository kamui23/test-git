<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Block;

class ProductBrand extends \Magento\Framework\View\Element\Template
{
    /**
     * @var $_brandFactory
     */
    protected $_brandFactory;
    /**
     * @var $registry
     */
    private $registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Icube\Brands\Model\BrandFactory $brandFactory,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_brandFactory = $brandFactory;
        $this->registry = $registry;
        parent::__construct($context);
    }

    public function getImageMediaPath()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        // return $this->getUrl('pub/media',['_secure' => $this->getRequest()->isSecure()]);
    }

    public function getBrand()
    {
        $product = $this->registry->registry('current_product');
        $collection = $this->_brandFactory->create()->getCollection();
        $collection->addFieldToFilter('attribute_id', $product->getBrand());

        return $collection->getFirstItem();
    }
}
