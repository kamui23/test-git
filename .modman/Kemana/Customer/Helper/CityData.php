<?php

namespace Kemana\Customer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class CityData extends AbstractHelper
{
    // Config section default value
    const CONFIG_SECTION_PATH = "general";
    // Config group default value
    const CONFIG_GROUP_PATH = "cityoptions";

    /**
     * @param      $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param      $field
     * @param null $storeId
     * @return mixed
     */
    public function getGeneralConfig($field, $storeId = null)
    {
        return $this->getConfigValue(self::CONFIG_SECTION_PATH . '/' . self::CONFIG_GROUP_PATH . '/' . $field, $storeId);
    }
}