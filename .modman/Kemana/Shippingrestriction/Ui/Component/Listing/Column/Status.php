<?php

namespace Kemana\Shippingrestriction\Ui\Component\Listing\Column;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $_options;
    protected $_statusList;

    public function __construct(\Kemana\Shippingrestriction\Model\System\Config\Status $statusList)
    {
        $this->_statusList = $statusList;
    }

    public function toOptionArray()
    {
        if ($this->_options === null) {
            $this->_options = array();
            foreach ($this->_statusList->toOptionArray() as $value => $label) {
                $this->_options[] = array(
                    'value' => $value,
                    'label' => $label
                );
            }
        }

        return $this->_options;
    }
}
