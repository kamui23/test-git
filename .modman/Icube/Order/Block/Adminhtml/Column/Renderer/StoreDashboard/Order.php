<?php

namespace Icube\Order\Block\Adminhtml\Column\Renderer\StoreDashboard;

class Order extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /** Url path */
    const ROW_EDIT_URL = 'sales/order/view/';

    protected $_urlBuilder;
    protected $_storeManager;
    // protected $_dateTimeFactory;
    // protected $_timezoneInterface;

    /**
     * @var string
     */
    private $_editUrl;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\UrlInterface $urlBuilder,
        // \Magento\Framework\Intl\DateTimeFactory $dateTimeFactory,
        // \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        array $data = [],
        $editUrl = self::ROW_EDIT_URL
    )
    {
        $this->_urlBuilder = $urlBuilder;
        // $this->_dateTimeFactory = $dateTimeFactory;
        // $this->_timezoneInterface = $timezoneInterface;
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
        $id = $row->getData('entity_id');
        $url = $this->_urlBuilder->getUrl($this->_editUrl, ['order_id' => $id]);
        // $date = $this->_timezoneInterface
        //                                 ->date(new \DateTime($value))
        //                                 ->format('M d, Y, h:i:s A');
        $output = '<a href="' . $url . '">' . $value . '</a>';
        return $output;
    }
}