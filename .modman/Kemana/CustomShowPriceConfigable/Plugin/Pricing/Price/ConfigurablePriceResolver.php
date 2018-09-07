<?php

namespace Kemana\CustomShowPriceConfigable\Plugin\Pricing\Price;

class ConfigurablePriceResolver
{
    private   $lowestPriceOptionsProvider;
    protected $priceResolver;
    protected $stockModel;
    protected $storeManager;
    protected $pointOfSale;

    public function __construct(
        \Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProvider $lowestPriceOptionsProvider,
        \Magento\ConfigurableProduct\Pricing\Price\FinalPriceResolver $priceResolver,
        \Wyomind\AdvancedInventory\Model\Stock $stockModel, \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\PointOfSale\Model\PointOfSale $pointOfSale)
    {
        $this->lowestPriceOptionsProvider = $lowestPriceOptionsProvider;
        $this->priceResolver = $priceResolver;
        $this->stockModel = $stockModel;
        $this->storeManager = $storeManager;
        $this->pointOfSale = $pointOfSale;
    }

    public function aroundResolvePrice(
        \Magento\ConfigurableProduct\Pricing\Price\ConfigurablePriceResolver $subject,
        \Closure $proceed,
        \Magento\Framework\Pricing\SaleableInterface $product
    )
    {
        $originalResult = $proceed($product);
        $storeIds[] = $this->storeManager->getStore()->getId();

        $placesCollection = $this->pointOfSale->getPlacesByStoreId($this->storeManager->getStore()->getId());
        $arrPlaces = $placesCollection->getAllIds();

        $price = null;

        foreach ($product->getTypeInstance()->getUsedProducts($product) as $subProduct) {
            $access = false;
            $idProduct = $subProduct->getId();
            foreach ($arrPlaces as $value) {
                $stockData = $this->stockModel->getStockByProductIdAndPlaceId($idProduct, $value);
                if ($stockData['quantity_in_stock']) {
                    $access = true;
                    break;
                }
            }

            if (!$access) continue;

            $productPrice = $this->priceResolver->resolvePrice($subProduct);
            $price = $price ? min($price, $productPrice) : $productPrice;

        }
        return $price === null ? (float)$originalResult : (float)$price;
    }


}