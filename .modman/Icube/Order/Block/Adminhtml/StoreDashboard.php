<?php

namespace Icube\Order\Block\Adminhtml;

class StoreDashboard extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_storeDashboard';/*block grid.php directory*/
        $this->_blockGroup = 'Icube_Order';
        $this->_headerText = __('StoreDashboard');
        parent::_construct();
        $this->removeButton('add');
    }
}