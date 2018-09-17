<?php
/**
 * Copyright © 2017 Icube. All rights reserved.
 */

namespace Icube\Brands\Model;

class Items extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Icube\Brands\Model\ResourceModel\Items');
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStoreId()
    {
        return $this->getData('store_ids');
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getCustomerGroupId()
    {
        return $this->getData('customer_group_ids');
    }


}
