<?php

namespace Icube\CancelOrder\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_orderCollectionFactory;
    protected $_logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Psr\Log\LoggerInterface $logger)
    {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_logger = $logger;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->_logger->info('Cancel pending/pending_payment order - Start');
        $time = strtotime('-24 hours');
        if (date('w', $time) % 6 == 0) {
            $time = strtotime('last weekday ' . date('H:i:s', $time), $time);
        }
        $time = date('Y-m-d H:i:s', $time);

        $collection = $this->_orderCollectionFactory->create()
                                                    ->addAttributeToSelect('*')
                                                    ->addFieldToFilter('status', ['pending', 'pending_payment'])
                                                    ->addFieldToFilter('created_at', ['lt' => $time]);

        foreach ($collection as $order) {
            $order->cancel()->save();
            $this->_logger->info('Order #' . $order->getIncrementId() . ' has been canceled');
        }
        $this->_logger->info('Cancel pending/pending_payment order - End');
    }
}