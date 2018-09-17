<?php

namespace Kemana\DealerPrice\Plugin\Block\Product\View\Type;

class Configurable
{
    protected $_jsonDecoder;
    protected $_jsonEncoder;
    protected $_helper;

    public function __construct(
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Kemana\DealerPrice\Helper\Data $helper
    )
    {
        $this->_jsonDecoder = $jsonDecoder;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_helper = $helper;
    }

    public function afterGetJsonConfig(\Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject, $result)
    {
        $currentProduct = $subject->getProduct();
        $specialPrice = $currentProduct->getPriceInfo()->getPrice('special_price')->getAmount()->getValue();
        $resultDecode = $this->_jsonDecoder->decode($result);

        if (!empty($specialPrice)) {
            $resultDecode['prices']['specialPrice'] = [
                'amount' => $this->_helper->_registerJsPrice($specialPrice),
            ];
        }

        $allowedProducts = $subject->getAllowProducts();
        foreach ($allowedProducts as $product) {
            $specialPrice = $product->getPriceInfo()->getPrice('special_price')->getAmount()->getValue();

            $resultDecode['optionPrices'][$product->getId()]['specialPrice'] = [
                'amount' => $this->_helper->_registerJsPrice(
                    $specialPrice
                )
            ];
        }

        return $this->_jsonEncoder->encode($resultDecode);

    }
}