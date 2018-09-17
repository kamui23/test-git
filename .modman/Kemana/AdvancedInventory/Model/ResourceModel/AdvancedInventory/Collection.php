<?php

namespace Kemana\AdvancedInventory\Model\ResourceModel\AdvancedInventory;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init('Kemana\AdvancedInventory\Model\AdvancedInventory', 'Kemana\AdvancedInventory\Model\ResourceModel\AdvancedInventory');
    }
}