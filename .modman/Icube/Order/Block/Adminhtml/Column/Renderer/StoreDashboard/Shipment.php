<?php

namespace Icube\Order\Block\Adminhtml\Column\Renderer\StoreDashboard;

class Shipment extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /** Url path */
    const ROW_EDIT_URL = 'sales/shipment/view/';

    protected $_storeManager;

    /**
     * @var string
     */
    private $_editUrl;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        array $data = [],
        $editUrl = self::ROW_EDIT_URL
    )
    {
        $this->_editUrl = $editUrl;
        parent::__construct($context);
    }

    /**
     * Render link for product
     *
     * @param  \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        $id = $row->getData('shipment_eid');
        if ($id != NULL) {
            $url = $this->_urlBuilder->getUrl($this->_editUrl, ['shipment_id' => $id]);
            $output = '<a href="' . $url . '">' . $value . '</a>';
            return $output;
        }
        $output = $value;
        return $output;
    }
}