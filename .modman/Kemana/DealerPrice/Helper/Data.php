<?php

namespace Kemana\DealerPrice\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_customerSession;
    protected $_customerContext;
    protected $_group;
    protected $_lowestPriceOptionsProvider;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $customerContext,
        \Magento\Customer\Model\Group $group,
        \Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProviderInterface $lowestPriceOptionsProvider
    )
    {
        $this->_customerSession = $customerSession;
        $this->_customerContext = $customerContext;
        $this->_group = $group;
        $this->_lowestPriceOptionsProvider = $lowestPriceOptionsProvider;
        parent::__construct($context);
    }

    public function isLoggedIn()
    {
        return $this->_customerContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function getCustomerGroupCode()
    {
        $id = $this->_customerSession->getCustomerGroupId();
        $group = $this->_group->load($id);
        return $group->getCustomerGroupCode();
    }

    public function hasSpecialPrice($block)
    {
        $displayRegularPrice = $block->getPriceType(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)->getAmount()->getValue();
        $displayFinalPrice = $block->getPriceType(\Magento\Catalog\Pricing\Price\TierPrice::PRICE_CODE)->getAmount()->getValue();
        if ($displayFinalPrice) {
            return $displayFinalPrice < $displayRegularPrice;
        }

        return false;
    }

    /**
     * Define if the special price should be shown
     *
     * @return bool
     */
    public function configurableHasSpecialPrice($block)
    {
        $product = $block->getSaleableItem();
        $subProducts = $this->_lowestPriceOptionsProvider->getProducts($product);
        foreach ($subProducts as $subProduct) {
            $regularPrice = $subProduct->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\RegularPrice::PRICE_CODE)->getValue();
            $finalPrice = $subProduct->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\TierPrice::PRICE_CODE)->getValue();
            if ($finalPrice) {
                return $finalPrice < $regularPrice;
            }
        }

        return false;
    }

    /**
     * Define if the special price should be shown
     *
     * @return bool
     */
    public function getConfigurableSpecialPrice($block)
    {
        $product = $block->getSaleableItem();
        $subProducts = $this->_lowestPriceOptionsProvider->getProducts($product);

        foreach ($subProducts as $subProduct) {
            $specialPrice = $subProduct->getPriceInfo()->getPrice(\Magento\Catalog\Pricing\Price\SpecialPrice::PRICE_CODE)->getAmount();
            if ($specialPrice) {
                return $specialPrice;
            }
        }
        return 0;
    }

    /**
     * Replace ',' on '.' for js
     *
     * @param float $price
     * @return string
     */
    public function _registerJsPrice($price)
    {
        return str_replace(',', '.', $price);
    }
}