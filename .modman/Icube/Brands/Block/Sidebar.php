<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Block;

class Sidebar extends \Magento\Framework\View\Element\Template
{

    protected $_brandFactory;
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Icube\Brands\Model\BrandFactory $brandFactory,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_brandFactory = $brandFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getGroupId()
    {
        if ($this->_customerSession->isLoggedIn()):
            $customerGroupId = $this->_customerSession->getCustomer()->getGroupId();
            return $customerGroupId;
        endif;

        return 0;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    public function getBrands()
    {
        $collection = $this->_brandFactory->create()->getCollection()->getActiveCollection();
        $charBarndArray = array();
        foreach ($collection as $brand) {
            $name = trim($brand->getName());
            $charBarndArray[strtoupper($name[0])][] = $brand;
        }

        return $charBarndArray;
    }


}
