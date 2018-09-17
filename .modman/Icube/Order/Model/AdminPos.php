<?php

namespace Icube\Order\Model;

use Magento\Framework\Model\AbstractModel;

class AdminPos extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Icube\Order\Model\ResourceModel\AdminPos::class);
    }
}