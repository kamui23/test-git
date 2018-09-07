<?php

namespace Icube\Order\Block\Adminhtml\Column\Renderer\StoreDashboard;

class Customer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /** Url path */
    const ROW_EDIT_URL = 'customer/index/edit/';

    protected $_urlBuilder;
    protected $_storeManager;

    /**
     * @var string
     */
    private $_editUrl;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $data = [],
        $editUrl = self::ROW_EDIT_URL
    )
    {
        $this->_urlBuilder = $urlBuilder;
        $this->_editUrl = $editUrl;
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
        $id = $row->getData('customer_id');
        if ($id != NULL) {
            $url = $this->_urlBuilder->getUrl($this->_editUrl, ['id' => $id]);
            $output = '<a href="' . $url . '">' . $value . '</a>';
            return $output;
        }
        $output = $value;
        return $output;
    }
}