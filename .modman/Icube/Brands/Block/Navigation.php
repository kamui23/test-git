<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Icube\Brands\Block;

use Magento\Catalog\Model\Category;
use Magento\Customer\Model\Context;

/**
 * Catalog navigation
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Navigation extends \Magento\Catalog\Block\Navigation
{
    private $ruleProvider;

    /**
     * Navigation constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Icube\Brands\Model\ProductRuleProvider $ruleProvider
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Helper\Category $catalogCategory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Icube\Brands\Model\ProductRuleProvider $ruleProvider,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $flatState,
        array $data = []
    )
    {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogLayer = $layerResolver->get();
        $this->httpContext = $httpContext;
        $this->ruleProvider = $ruleProvider;
        $this->_catalogCategory = $catalogCategory;
        $this->_registry = $registry;
        $this->flatState = $flatState;
        $this->_categoryInstance = $categoryFactory->create();
        parent::__construct(
            $context,
            $categoryFactory,
            $productCollectionFactory,
            $layerResolver,
            $httpContext,
            $catalogCategory,
            $registry,
            $flatState,
            $data
        );
    }

    /**
     * Retrieve child categories of current category
     *
     * @return Category[]|\Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getCurrentChildCategories()
    {
        $productIds = $this->ruleProvider->getRestrictedProductIds();
        $categories = $this->_catalogLayer->getCurrentCategory()->getChildrenCategories();
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $this->_productCollectionFactory->create();
        $productCollection->getSelect()->where('e.entity_id NOT IN (' . implode(",", $productIds) . ')');
        $this->_catalogLayer->prepareProductCollection($productCollection);
        $productCollection->addCountToCategories($categories);
        return $categories;
    }

}
