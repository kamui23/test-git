<?php

namespace Kemana\Shippingrestriction\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Rule extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('kemana_shippingrestriction_rule', 'rule_id');
    }

}
