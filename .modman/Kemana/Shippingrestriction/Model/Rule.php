<?php

namespace Kemana\Shippingrestriction\Model;

use Kemana\Shippingrestriction\Helper\Data;

class Rule extends \Magento\Framework\Model\AbstractModel
{
    const ALL_ORDERS      = 0;
    const BACKORDERS_ONLY = 1;
    const NON_BACKORDERS  = 2;

    protected $_storeManager;
    protected $_srhelper;

    /**
     * Model constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_storeManager = $storeManager;
        $this->_srhelper = $helper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('Kemana\Shippingrestriction\Model\ResourceModel\Rule');
    }

    public function restrict($method)
    {
        return (false !== strpos($this->getMethods(), ',' . $method->getCode() . ','));
    }

    protected function _setWebsiteIds()
    {
        $websites = array();

        foreach ($this->_storeManager->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                foreach ($stores as $store) {
                    $websites[$store->getId()] = $website->getId();
                }
            }
        }

        $this->setOrigData('website_ids', $websites);
    }


    public function beforeSave()
    {
        $this->_setWebsiteIds();
        return parent::beforeSave();
    }

    public function beforeDelete()
    {
        $this->_setWebsiteIds();
        return parent::beforeDelete();
    }

    public function activate()
    {
        $this->setIsActive($this->_srhelper::ACTIVE);
        $this->save();
        return $this;
    }

    public function inactivate()
    {
        $this->setIsActive($this->_srhelper::INACTIVE);
        $this->save();
        return $this;
    }
}
