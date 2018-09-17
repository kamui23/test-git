<?php

namespace Kemana\FixSearchCategoryByMenu\Plugin\Catalog\Model;

class Autocomplete
{
    protected $_categoryRepository;
    protected $_helper;

    public function __construct(\Magento\Catalog\Model\CategoryRepository $categoryRepository, \Kemana\FixSearchCategoryByMenu\Helper\Data $helper)
    {
        $this->_categoryRepository = $categoryRepository;
        $this->_helper = $helper;
    }

    public function afterGetItems(\Magento\Search\Model\Autocomplete $subject, $result)
    {
        $categories = $this->_helper->getMenuCategories();
        if ($categories) {
            foreach ($result as $key => $item) {
                $arrayData = $item->toArray();
                if ($arrayData['type'] == 'category') {
                    $categoryId = $arrayData['id'];
                    $categoryCur = $this->_categoryRepository->get($categoryId);
                    $parentCatId = $categoryCur->getParentId();
                    if (!in_array($parentCatId, $categories)) {
                        unset($result[$key]);
                    }

                }
            }
        }

        return $result;
    }


}