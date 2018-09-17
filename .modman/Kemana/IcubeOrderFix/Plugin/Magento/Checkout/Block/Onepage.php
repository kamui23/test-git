<?php
/**
 * Created by PhpStorm.
 * User: muttaqiin
 * Date: 4/26/18
 * Time: 5:24 PM
 */

namespace Kemana\IcubeOrderFix\Plugin\Magento\Checkout\Block;

use Magento\Framework\Url;

class Onepage
{
    const STORE_INFO_URL = "icubeorder/item/storeinfo";

    public function beforeToHtml(\Magento\Checkout\Block\Onepage $subject)
    {
        $store_info_url = $subject->getUrl(self::STORE_INFO_URL);
        $subject->setData('store_info_url', $store_info_url);
    }
}