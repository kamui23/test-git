<?php
namespace Icube\Order\Model\Rewrite\Order\Email\Sender;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    /**
     * Prepare email template with variables
     *
     * @param \Magento\Sales\Model\Order $order
     * @return void
     */
    protected function prepareTemplate(\Magento\Sales\Model\Order $order)
    {
        $isMixed = ($order->getDeliveryPickup() == 'mixed') ? TRUE : FALSE;
        $pickupInfo = NULL;
        if ($order->getDeliveryPickup() == 'mixed') {
            $storecode = NULL;
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            $storecode = $order->getStoreCode();
            if($storecode != NULL){
                $pos = $om->create('Wyomind\PointOfSale\Model\PointOfSale')->load($storecode,'store_code');
                $pickupInfo = $pos->getName();
            }
        }
        $transport = [
            'order' => $order,
            'billing' => $order->getBillingAddress(),
            'payment_html' => $this->getPaymentHtml($order),
            'store' => $order->getStore(),
            'formattedShippingAddress' => $this->getFormattedShippingAddress($order),
            'formattedBillingAddress' => $this->getFormattedBillingAddress($order),
            'isMixed' => $isMixed,
            'pickupInfo' => $pickupInfo

        ];
        $transport = new \Magento\Framework\DataObject($transport);

        $this->eventManager->dispatch(
            'email_order_set_template_vars_before',
            ['sender' => $this, 'transport' => $transport]
        );

        $this->templateContainer->setTemplateVars($transport->getData());

        \Magento\Sales\Model\Order\Email\Sender::prepareTemplate($order);
    }
}
