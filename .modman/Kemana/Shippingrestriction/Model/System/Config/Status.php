<?php

namespace Kemana\Shippingrestriction\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;
use Kemana\Shippingrestriction\Helper\Data;

class Status implements ArrayInterface
{
    const ACTIVE   = 1;
    const INACTIVE = 0;

    protected $_srhelper;

    public function __construct(Data $helper)
    {
        $this->_srhelper = $helper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            $this->_srhelper::ACTIVE   => __('Active'),
            $this->_srhelper::INACTIVE => __('Inactive')
        ];

        return $options;
    }
}
