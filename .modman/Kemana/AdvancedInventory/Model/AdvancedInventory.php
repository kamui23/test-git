<?php

namespace Kemana\AdvancedInventory\Model;

use Magento\Framework\Model\AbstractModel;

class AdvancedInventory extends AbstractModel
{
    public function _construct()
    {
        $this->_init('Kemana\AdvancedInventory\Model\ResourceModel\AdvancedInventory');
    }
}