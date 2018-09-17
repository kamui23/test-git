<?php

namespace Kemana\Shippingrestriction\Ui\Component\Listing\Column;

class Method implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $_options;
    protected $_srhelper;

    public function __construct(
        \Kemana\Shippingrestriction\Helper\Data $helper
    )
    {
        $this->_srhelper = $helper;
    }

    public function toOptionArray()
    {
        if ($this->_options === null) {
            $this->_options = $this->_srhelper->getShippingMethods();
        }

        return $this->_options;
    }
}
