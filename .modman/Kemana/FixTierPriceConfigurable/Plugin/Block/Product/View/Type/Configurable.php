<?php

namespace Kemana\FixTierPriceConfigurable\Plugin\Block\Product\View\Type;

class Configurable
{
    protected $_helper;
    protected $_jsonEncoder;
    protected $_jsonDecoder;
    protected $_priceHelper;

    /**
     * @param \Magento\ConfigurableProduct\Helper\Data $helper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Json\DecoderInterface $jsonDecoder
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     */
    public function __construct(
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    )
    {
        $this->_helper = $helper;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_jsonDecoder = $jsonDecoder;
        $this->_priceHelper = $priceHelper;
    }

    public function afterGetJsonConfig(\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject, $result)
    {
        $store = $subject->getCurrentStore();
        $resultDecode = $this->_jsonDecoder->decode($result);
        $resultDecode['currencyFormat'] = $store->getCurrentCurrency()->getOutputFormat();
        $allowedProducts = $subject->getAllowProducts();
        foreach ($allowedProducts as $product) {
            $tierPrices = [];
            $priceInfo = $product->getPriceInfo();

            $tierPriceModel = $priceInfo->getPrice('tier_price');
            $tierPricesList = $tierPriceModel->getTierPriceList();

            foreach ($tierPricesList as $tierPrice) {
                $tierPrices[] = [
                    'qty'            => $this->_registerJsPrice($tierPrice['price_qty']),
                    'price'          => $this->_registerJsPrice($tierPrice['price']->getValue()),
                    'priceFormatted' => $this->_registerJsPrice($this->_priceHelper->currency($tierPrice['price']->getValue(), true, false)),
                    'percentage'     => $this->_registerJsPrice($tierPriceModel->getSavePercent($tierPrice['price'])),
                ];
            }

            $resultDecode['optionPrices'][$product->getId()]['tierPrices'] = $tierPrices;
        }

        return $this->_jsonEncoder->encode($resultDecode);

    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    protected function _registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }
}