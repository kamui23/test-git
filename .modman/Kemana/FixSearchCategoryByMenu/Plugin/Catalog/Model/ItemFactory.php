<?php

namespace Kemana\FixSearchCategoryByMenu\Plugin\Catalog\Model;

class ItemFactory
{
    public function beforeCreate(\Smile\ElasticsuiteCatalog\Model\Autocomplete\Category\ItemFactory $subject, $data)
    {
        $data['id'] = $data['category']->getId();
        return [$data];
    }
}