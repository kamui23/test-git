<?php

namespace Kemana\IcubeOrderFix\Plugin\Wyomind\PointOfSale\Helper;

use Magento\Framework\Url;

class Data
{
    /**
     * @var Url $_url
     */
    protected $_url;

    public function __construct(Url $url)
    {
        $this->_url = $url;
    }

    public function aroundGetStoreListAjaxUrl(\Wyomind\PointOfSale\Helper\Data $subject, callable $callable)
    {
        $used_urlStore = ltrim($subject::STORE_LIST_AJAX, "/");

        return $this->_url->getUrl($used_urlStore);
    }
}