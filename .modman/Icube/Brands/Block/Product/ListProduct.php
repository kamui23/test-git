<?php

namespace Icube\Brands\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    private $ruleProvider;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Icube\Brands\Model\ProductRuleProvider $ruleProvider,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    )
    {
        $this->_catalogLayer = $layerResolver->get();
        $this->ruleProvider = $ruleProvider;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * Retrieve loaded category collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Eav\Model\Entity\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getProductCollection()
    {
        $productIds = $this->ruleProvider->getRestrictedProductIds();
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            /* @var $layer \Magento\Catalog\Model\Layer */
            if ($this->getShowRootCategory()) {
                $this->setCategoryId($this->_storeManager->getStore()->getRootCategoryId());
            }

            // if this is a product view page
            if ($this->_coreRegistry->registry('product')) {
                // get collection of categories this product is associated with
                $categories = $this->_coreRegistry->registry('product')
                                                  ->getCategoryCollection()->setPage(1, 1)
                                                  ->load();
                // if the product is associated with any category
                if ($categories->count()) {
                    // show products from this category
                    $this->setCategoryId(current($categories->getIterator()));
                }
            }

            $origCategory = null;
            if ($this->getCategoryId()) {
                try {
                    $category = $this->categoryRepository->get($this->getCategoryId());
                } catch (NoSuchEntityException $e) {
                    $category = null;
                }

                if ($category) {
                    $origCategory = $layer->getCurrentCategory();
                    $layer->setCurrentCategory($category);
                }
            }
            $this->_productCollection = $layer->getProductCollection();

            // $this->prepareSortableFieldsByCategory($layer->getProductCollection());

            if ($origCategory) {
                $layer->setCurrentCategory($origCategory);
            }
        }

        // return $this->_productCollection->getSelect()->where('e.entity_id NOT IN ('.implode(",",$productIds).')');
        // return $this->_productCollection;
//        $this->_productCollection->addAttributeToFilter('entity_id',['nin'=>$productIds]);
        // echo "<pre>";
        // print_r($this->_productCollection->getSelect()->__toString());
        // exit;
        return $this->_productCollection;
    }
}

?>