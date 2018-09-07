<?php

namespace Icube\Order\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AdminPos extends AbstractDb
{

    protected function _construct()
    {
        $this->_init('icube_admin_pos', 'entity_id');
    }
}