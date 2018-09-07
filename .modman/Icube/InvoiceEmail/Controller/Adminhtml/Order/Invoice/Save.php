<?php

namespace Icube\InvoiceEmail\Controller\Adminhtml\Order\Invoice;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Sales\Controller\Adminhtml\Order\Invoice\Save
{
    protected $registry;
    protected $invoiceSender;
    protected $shipmentSender;
    protected $shipmentFactory;
    protected $invoiceService;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Sales\Model\Order\Email\Sender\ShipmentSender $shipmentSender,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService
    )
    {
        $this->registry = $registry;
        $this->invoiceSender = $invoiceSender;
        $this->shipmentSender = $shipmentSender;
        $this->shipmentFactory = $shipmentFactory;
        $this->invoiceService = $invoiceService;
        parent::__construct($context, $registry, $invoiceSender, $shipmentSender, $shipmentFactory, $invoiceService);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $formKeyIsValid = $this->_formKeyValidator->validate(parent::getRequest());
        $isPost = parent::getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__('We can\'t save the invoice right now.'));
            return $resultRedirect->setPath('sales/order/index');
        }

        $data = parent::getRequest()->getPost('invoice');
        $orderId = parent::getRequest()->getParam('order_id');

        if (!empty($data['comment_text'])) {
            $this->_objectManager->get('Magento\Backend\Model\Session')->setCommentText($data['comment_text']);
        }

        try {
            $invoiceData = parent::getRequest()->getParam('invoice', []);
            $invoiceItems = isset($invoiceData['items']) ? $invoiceData['items'] : [];
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            if (!$order->getId()) {
                throw new LocalizedException(__('The order no longer exists.'));
            }

            if (!$order->canInvoice()) {
                throw new LocalizedException(
                    __('The order does not allow an invoice to be created.')
                );
            }

            $invoice = $this->invoiceService->prepareInvoice($order, $invoiceItems);

            if (!$invoice) {
                throw new LocalizedException(__('We can\'t save the invoice right now.'));
            }

            if (!$invoice->getTotalQty()) {
                throw new LocalizedException(
                    __('You can\'t create an invoice without products.')
                );
            }
            $this->registry->register('current_invoice', $invoice);
            if (!empty($data['capture_case'])) {
                $invoice->setRequestedCaptureCase($data['capture_case']);
            }

            if (!empty($data['comment_text'])) {
                $invoice->addComment(
                    $data['comment_text'],
                    isset($data['comment_customer_notify']),
                    isset($data['is_visible_on_front'])
                );

                $invoice->setCustomerNote($data['comment_text']);
                $invoice->setCustomerNoteNotify(isset($data['comment_customer_notify']));
            }

            $invoice->register();

            $invoice->getOrder()->setCustomerNoteNotify(!empty($data['send_email']));
            $invoice->getOrder()->setIsInProcess(true);

            $transactionSave = $this->_objectManager->create(
                'Magento\Framework\DB\Transaction'
            )->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $shipment = false;
            if (!empty($data['do_shipment']) || (int)$invoice->getOrder()->getForcedShipmentWithInvoice()) {
                $shipment = parent::_prepareShipment($invoice);
                if ($shipment) {
                    $transactionSave->addObject($shipment);
                }
            }
            $transactionSave->save();

            $isCreatedInvoice = true;
            if (isset($shippingResponse) && $shippingResponse->hasErrors()) {
                $this->messageManager->addError(
                    __(
                        'The invoice and the shipment  have been created. ' .
                        'The shipping label cannot be created now.'
                    )
                );
                $isCreatedInvoice = false;
            } elseif (!empty($data['do_shipment'])) {
                $this->messageManager->addSuccess(__('You created the invoice and shipment.'));
                $isCreatedInvoice = false;
            }
            if($isCreatedInvoice) {
                $this->messageManager->addSuccess(__('The invoice has been created.'));
            }

            // send invoice/shipment emails
            try {
                //Icube Custom - Send email
                // if (!empty($data['send_email'])) {
                $this->invoiceSender->send($invoice);
                // }
                //End of Icube Custom
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError(__('We can\'t send the invoice email right now.'));
            }
            if ($shipment) {
                try {
                    if (!empty($data['send_email'])) {
                        $this->shipmentSender->send($shipment);
                    }
                } catch (\Exception $e) {
                    $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                    $this->messageManager->addError(__('We can\'t send the shipment right now.'));
                }
            }
            $this->_objectManager->get('Magento\Backend\Model\Session')->getCommentText(true);
            return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t save the invoice right now.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
        return $resultRedirect->setPath('sales/*/new', ['order_id' => $orderId]);
    }
}