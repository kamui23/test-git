<?php

namespace Kemana\Shippingrestriction\Helper;

use \Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const JNE_CARRIER            = "jnetrucking";
    const JNE_METHOD             = "jnetruckingrate0";
    const ADVANCE_RATE_CARRIER   = "advancerate";
    const ADVANCE_RATE_METHOD    = "advancerate0";
    const POS_RATE_CARRIER       = "posindonesia";
    const POS_RATE_METHOD        = "posindonesia0";
    const SUNDAY                 = 7;
    const MONDAY                 = 1;
    const TUESDAY                = 2;
    const WEDNESDAY              = 3;
    const THURSDAY               = 4;
    const FRIDAY                 = 5;
    const SATURDAY               = 6;
    const PROMPT                 = 'Please select...';
    const RULE_REGISTRY          = 'current_kemana_shippingrestriction_rule';
    const DAYS_TITLE             = 'Days';
    const RESTRICTIONS           = 'Restrictions';
    const SHIPPING_RESTRICTIONS  = 'Shipping Restrictions';
    const STORES_AND_CUST_GROUPS = 'Stores & Customer Groups';
    const FORM_ELEMENT_ID        = 'edit_form';
    const RECORD_UPDATED_MESSAGE = 'Record(s) have been updated.';
    const ACTIVE                 = 1;
    const INACTIVE               = 0;

    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory
    )
    {
        $this->_carrierFactory = $carrierFactory;
        parent::__construct($context);

    }

    public function getShippingMethods()
    {
        $activeCarriers = $this->getActiveCarriers();
        $methods = array();
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $carrierMethods = $carrierModel->getAllowedMethods();
            $carrierTitle = $this->scopeConfig->getValue('carriers/' . $carrierCode . '/title');
            foreach ($carrierMethods as $methodCode => $method) {
                if ($carrierCode == self::JNE_CARRIER) {
                    $methodCode = self::JNE_METHOD;
                }
                if ($carrierCode == self::ADVANCE_RATE_CARRIER) {
                    $methodCode = self::ADVANCE_RATE_METHOD;
                }
                if ($carrierCode == self::POS_RATE_CARRIER) {
                    $methodCode = self::POS_RATE_METHOD;
                }
                $code = $carrierCode . '_' . $methodCode;
                $methods[] = array('value' => $code, 'label' => $carrierCode . ' - ' . $carrierTitle . ' - ' . $method);
            }
        }
        return $methods;
    }

    public function getAllDays()
    {
        return array(
            array('value' => self::SUNDAY, 'label' => __('Sunday')),
            array('value' => self::MONDAY, 'label' => __('Monday')),
            array('value' => self::TUESDAY, 'label' => __('Tuesday')),
            array('value' => self::WEDNESDAY, 'label' => __('Wednesday')),
            array('value' => self::THURSDAY, 'label' => __('Thursday')),
            array('value' => self::FRIDAY, 'label' => __('Friday')),
            array('value' => self::SATURDAY, 'label' => __('Saturday')),
        );
    }

    public function getAllTimes()
    {
        $timeArray = array();
        $timeArray[0] = self::PROMPT;

        for ($i = 0; $i < 24; $i++) {
            for ($j = 0; $j < 60; $j = $j + 15) {
                $timeStamp = $i . ':' . $j;
                $timeFormat = date('H:i', strtotime($timeStamp));
                $timeArray[$i * 100 + $j + 1] = $timeFormat;
            }
        }
        return $timeArray;
    }

    /**
     * Retrieve active system carriers
     *
     * @param   mixed $store
     * @return  array
     */
    public function getActiveCarriers($store = null)
    {
        $carriers = [];
        $config = $this->scopeConfig->getValue('carriers', $this->scopeConfig::SCOPE_TYPE_DEFAULT, $store);
        foreach (array_keys($config) as $carrierCode) {
            if ($this->scopeConfig->isSetFlag('carriers/' . $carrierCode . '/active', $this->scopeConfig::SCOPE_TYPE_DEFAULT, $store)) {
                $carrierModel = $this->_carrierFactory->create($carrierCode, $store);
                if ($carrierModel) {
                    $carriers[$carrierCode] = $carrierModel;
                }
            }
        }
        return $carriers;
    }

    public function getUrl()
    {

    }
}
