<?php

namespace Kemana\Shippingrestriction\Ui\Component\Listing\Column;

class Store implements \Magento\Framework\Data\OptionSourceInterface
{
    const LABEL = 'Restricts In All';

    protected $_options;
    protected $_store;

    public function __construct(
        \Magento\Store\Model\ResourceModel\Store\Collection $store
    )
    {
        $this->_store = $store;
    }

    public function toOptionArray()
    {
        if ($this->_options === null) {
            $this->_options = $this->_store->toOptionArray();
            $this->_options[] = array(
                'value' => 'all',
                'label' => __(self::LABEL)
            );
        }

        return $this->_options;
    }
}
