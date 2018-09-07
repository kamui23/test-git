<?php

namespace Kemana\FixSearchCategoryByMenu\Plugin\Product\Fulltext;

class Collection
{
    protected $_helper;

    public function __construct(\Kemana\FixSearchCategoryByMenu\Helper\Data $helper)
    {
        $this->_helper = $helper;
    }

    public function aroundGetFacetedData(\Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\Collection $subject, \Closure $proceed, $field)
    {
        $originalResult = $proceed($field);
        if ($field == 'categories') {
            $categoriesMenu = $this->_helper->getMenuCategories();
            foreach ($originalResult as $key => $val) {
                if (!in_array($key, $categoriesMenu)) unset($originalResult[$key]);
            }
            return $originalResult;
        }
        return $originalResult;

    }

}