<?php

namespace Kemana\Shippingrestriction\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;

class NewAction extends \Kemana\Shippingrestriction\Controller\Adminhtml\Rule
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
