<?php

namespace Kemana\FixSpecialPriceConfigurable\Plugin\Pricing\Price;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\LinkedProductSelectBuilderInterface;
use Magento\Framework\App\ResourceConnection;

class LowestPriceOptionsProvider
{
    /**
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * @var LinkedProductSelectBuilderInterface
     */
    protected $_linkedProductSelectBuilder;

    /**
     * @var ProductInterface
     */
    protected $_product;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Key is product id. Value is prepared product collection
     *
     * @var array
     */
    private $__productsMap;

    public function __construct(
        ResourceConnection $resourceConnection,
        LinkedProductSelectBuilderInterface $linkedProductSelectBuilder,
        CollectionFactory $collectionFactory,
        ProductInterface $product
    )
    {
        $this->_resource = $resourceConnection;
        $this->_linkedProductSelectBuilder = $linkedProductSelectBuilder;
        $this->_collectionFactory = $collectionFactory;
        $this->_product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function aroundGetProducts(\Magento\ConfigurableProduct\Pricing\Price\LowestPriceOptionsProvider $subject, \Closure $proceed, $product)
    {
        $productIds = $this->_resource->getConnection()->fetchCol(
            '(' . implode(') UNION (', $this->_linkedProductSelectBuilder->build($product->getId())) . ')'
        );

        $this->__productsMap[$product->getId()] = $this->_collectionFactory->create()
                                                                           ->addAttributeToSelect(
                                                                               ['price', 'special_price', 'special_from_date', 'special_to_date', 'tax_class_id']
                                                                           )
                                                                           ->addIdFilter($productIds)
                                                                           ->getItems();

        return $this->__productsMap[$product->getId()];
    }
}