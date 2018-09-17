<?php

namespace Icube\JneTrucking\Plugin;

use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;

class Converter
{
    private $extensionFactory;

    public function __construct(ShippingMethodExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    public function aroundModelToDataObject(
        \Magento\Quote\Model\Cart\ShippingMethodConverter $subject,
        \Closure $proceed,
        $rateModel,
        $quoteCurrencyCode)
    {
        $result = $proceed($rateModel, $quoteCurrencyCode);
        $extensibleAttribute = ($result->getExtensionAttributes())
            ? $result->getExtensionAttributes()
            : $this->extensionFactory->create();

        $extensibleAttribute->setEstimation(($rateModel->getMethodDescription() != null) ? $rateModel->getMethodDescription() : "");
        $result->setExtensionAttributes($extensibleAttribute);

        return $result;
    }
}