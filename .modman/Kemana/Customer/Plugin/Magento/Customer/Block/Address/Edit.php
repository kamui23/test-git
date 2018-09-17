<?php

namespace Kemana\Customer\Plugin\Magento\Customer\Block\Address;

use Kemana\Customer\Helper\CityData;

class Edit
{
    /** @var CityData $_cityHelperData */
    protected $_cityHelperData;

    /**
     * Edit constructor.
     * @param CityData $cityData
     */
    public function __construct(CityData $cityData)
    {
        $this->_cityHelperData = $cityData;
    }

    /**
     * @param \Magento\Customer\Block\Address\Edit $subject
     */
    public function beforeToHtml(\Magento\Customer\Block\Address\Edit $subject)
    {
        $subject->setData('enable_city', $this->_cityHelperData->getGeneralConfig('enable'));
    }
}
