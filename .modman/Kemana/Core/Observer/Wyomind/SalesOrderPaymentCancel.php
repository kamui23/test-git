<?php

namespace Kemana\Core\Observer\Wyomind;

class SalesOrderPaymentCancel extends \Wyomind\AdvancedInventory\Observer\SalesOrderPaymentCancel
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getPayment()->getOrder();
        // Icube custom - disable stock return
        // $this->_modelAssignation->cancel($order->getEntityId());
    }
}