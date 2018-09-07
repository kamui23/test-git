<?php

namespace Kemana\Customer\Plugin\Mageplaza\Osc\Block\Checkout;

use Kemana\Customer\Helper\CityData;
use Magento\Directory\Helper\Data;
use Magento\Directory\Model\AllowedCountries;
use Magento\Directory\Model\CountryFactory;

class LayoutProcessor
{
    /** @var CityData $_cityDataHelper */
    protected $_cityDataHelper;

    /** @var AllowedCountries $_allowedCountries */
    protected $_allowedCountries;

    /** @var CountryFactory $_countryFactory */
    protected $_countryFactory;

    /** @var Data $_directoryData */
    protected $_directoryData;

    /**
     * LayoutProcessor constructor.
     * @param CountryFactory $countryFactory
     * @param AllowedCountries $allowedCountries
     * @param Data $directoryData
     * @param CityData $cityData
     */
    public function __construct(
        CountryFactory $countryFactory,
        AllowedCountries $allowedCountries,
        Data $directoryData,
        CityData $cityData)
    {
        $this->_cityDataHelper = $cityData;
        $this->_allowedCountries = $allowedCountries;
        $this->_countryFactory = $countryFactory;
        $this->_directoryData = $directoryData;
    }

    /**
     * @param \Mageplaza\Osc\Block\Checkout\LayoutProcessor $subject
     * @param                                               $result
     * @return mixed
     */
    public function afterGetAddressFieldset(\Mageplaza\Osc\Block\Checkout\LayoutProcessor $subject, $result)
    {
        $fields = $result;
        $cityOptionEnabled = $this->_cityDataHelper->getGeneralConfig('enable');

        /**
         * Temporary fix country issue on the checkout page.
         * Should be removed once upgraded to Magento 2 latest version.
         */

        $allowedCountries = $this->_allowedCountries->getAllowedCountries();

        $countryOptions = $this->_rebuildCountryOptions($allowedCountries);

        if ($fields['country_id']) {
            unset($fields['country_id']['options']);
            $fields['country_id']['options'] = $countryOptions;
        }

        /**
         * End of fixes
         */

        if (!$cityOptionEnabled) {
            foreach ($fields as $code => &$field) {
                if ($code == 'city') {
                    unset($fields[$code]);
                }
            }
        }

        return $fields;
    }

    /**
     * Rebuild country options as it not working on the checkout page.
     *
     * @param $allowedCountries
     * @return array
     */
    protected function _rebuildCountryOptions($allowedCountries)
    {
        $options = array();
        $options[] = ['value' => '', 'label' => ''];

        $countries = $this->_countryFactory->create();

        if ($allowedCountries) {
            foreach ($allowedCountries as $allowedCountry) {
                $country = $countries->loadByCode($allowedCountry);
                $options[] = [
                    'value'              => $allowedCountry,
                    'label'              => $country->getName(),
                    'is_region_required' => $this->_directoryData->isRegionRequired($allowedCountry)
                ];
            }
        }

        return $options;
    }
}
