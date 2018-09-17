<?php

namespace Icube\Order\Model\ResourceModel\AdminPos;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Icube\Order\Model\AdminPos',
            'Icube\Order\Model\ResourceModel\AdminPos'
        );
    }
}
