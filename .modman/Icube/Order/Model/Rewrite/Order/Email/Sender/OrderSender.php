<?php
namespace Icube\Order\Model\Rewrite\Order\Email\Sender;

use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{
    protected $_objectManager;

    public function __construct(
        Template $templateContainer,
        OrderIdentity $identityContainer,
        Order\Email\SenderBuilderFactory $senderBuilderFactory,
        \Psr\Log\LoggerInterface $logger,
        Renderer $addressRenderer,
        PaymentHelper $paymentHelper,
        OrderResource $orderResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $globalConfig,
        ManagerInterface $eventManager,
        \Magento\Framework\App\ObjectManager $objectManager
    ) {
        parent::__construct($templateContainer, $identityContainer, $senderBuilderFactory, $logger, $addressRenderer, $paymentHelper, $orderResource, $globalConfig, $eventManager);
        $this->_objectManager = $objectManager;
    }

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
            $storecode = $order->getStoreCode();
            if($storecode != NULL){
                $pos = $this->_objectManager->create('Wyomind\PointOfSale\Model\PointOfSale')->load($storecode,'store_code');
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
