<?php

namespace Kemana\AdvancedInventory\Helper;

class Configurable extends \Magento\ConfigurableProduct\Helper\Data
{
    protected $_objectManager;

    public function __construct(
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\App\ObjectManager $objectManager
    ) {
        parent::__construct($imageHelper);
        $this->_objectManager = $objectManager;
    }


    public function getStock($productId, $storeCode, $groupId)
    {
        $pointOfSaleCollection = $this->_objectManager->create('Wyomind\PointOfSale\Model\ResourceModel\PointOfSale\Collection');
        $pointOfSaleCollection->getSelect()->joinLeft(["lsp" => 'advancedinventory_item'], "lsp.product_id = " . $productId)
                              ->joinLeft(
                                  [
                                      "stocks" => 'advancedinventory_stock'],
                                  "stocks.place_id = main_table.place_id AND stocks.product_id='$productId'",
                                  [
                                      "qty" => "SUM(stocks.quantity_in_stock )"
                                  ]
                              );

        $pointOfSaleCollection->addFieldToFilter("main_table.country_code", strtoupper($storeCode));
        $pointOfSaleCollection->addFieldToFilter("main_table.customer_group", array("finset" => $groupId));
        $pointOfSaleCollection->getSelect()
                              ->group('product_id')
                              ->limit(1);
        return $pointOfSaleCollection->getFirstItem();
    }

    public function getOptions($currentProduct, $allowedProducts)
    {
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $options = [];
        $storeId = $storeManager->getStore()->getId();
        $storeCode = $storeManager->getStore()->getCode();
        $customerModel = $this->_objectManager->get('Magento\Customer\Model\Customer');

        $session = 'customer_' . $storeCode . '_website';

        $loggedOut = true;
        if ($this->isLoggedIn()) {
            $customer = $customerModel->load($_SESSION[$session]['customer_id']);
            $groupId = $customer->getGroupId();
            $loggedOut = false;
        }
        if($loggedOut) {
            $groupId = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
        }

        foreach ($allowedProducts as $product) {
            $productId = $product->getId();
            $images = $this->getGalleryImages($product);
            $productId = $product->getId();

            $stock = $this->getStock($productId, $storeCode, $groupId);

            if ($stock->getQty() == 0) continue;

            if ($images) {
                foreach ($images as $image) {
                    $options['images'][$productId][] =
                        [
                            'thumb'    => $image->getData('small_image_url'),
                            'img'      => $image->getData('medium_image_url'),
                            'full'     => $image->getData('large_image_url'),
                            'caption'  => $image->getLabel(),
                            'position' => $image->getPosition(),
                            'isMain'   => $image->getFile() == $product->getImage(),
                        ];
                }
            }
            foreach ($this->getAllowAttributes($currentProduct) as $attribute) {
                $productAttribute = $attribute->getProductAttribute();
                $productAttributeId = $productAttribute->getId();
                $attributeValue = $product->getData($productAttribute->getAttributeCode());

                $options[$productAttributeId][$attributeValue][] = $productId;
                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }
        return $options;
    }

    public function isLoggedIn()
    {
        $context = $this->_objectManager->get('Magento\Framework\App\Http\Context');
        return $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
}