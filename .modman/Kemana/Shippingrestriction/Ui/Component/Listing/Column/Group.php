<?php

namespace Kemana\Shippingrestriction\Ui\Component\Listing\Column;

class Group implements \Magento\Framework\Data\OptionSourceInterface
{
    const LABEL = 'Restricts For All';
    protected $_options;
    protected $_group;

    public function __construct(
        \Magento\Customer\Model\Customer\Attribute\Source\Group $group
    )
    {
        $this->_group = $group;
    }

    public function toOptionArray()
    {
        if ($this->_options === null) {
            $this->_options = $this->_group->toOptionArray();
            $this->_options[] = array(
                'value' => 'all',
                'label' => __(self::LABEL)
            );
        }

        return $this->_options;
    }
}
