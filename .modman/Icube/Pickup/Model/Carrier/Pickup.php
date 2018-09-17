<?php

namespace Icube\Pickup\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Pickup extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'pickup';
    protected $_cartFactory;
    protected $state;
    protected $_rateResultFactory;
    protected $_rateMethodFactory;

    /**
     * Pickup constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Magento\Checkout\Model\CartFactory $cartFactory
     * @param \Magento\Framework\App\State $state
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Checkout\Model\CartFactory $cartFactory,
        \Magento\Framework\App\State $state,
        array $data = []
    )
    {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_cartFactory = $cartFactory;
        $this->state = $state;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return ['pickup' => $this->getConfigData('name')];
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $quote = $this->_cartFactory->create()->getQuote();

        $deliverypickup = $quote->getDeliveryPickup();
        if ('adminhtml' != $this->state->getAreaCode() && $deliverypickup != 'pickup') {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier('pickup');
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod('pickup');
        $method->setMethodTitle($this->getConfigData('name'));

        /*you can fetch shipping price from different sources over some APIs, we used price from config.xml - xml node price*/
        $amount = $this->getConfigData('price');

        $method->setPrice($amount);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }
}