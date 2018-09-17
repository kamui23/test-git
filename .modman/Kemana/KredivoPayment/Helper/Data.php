<?php

namespace Kemana\KredivoPayment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    const ENV = 'production';

    // Default response url path
    const RESPONSE_URL_PATH = "kredivo/payment/response";

    // Default notification url path
    const NOTIFICATION_URL_PATH = "kredivo/payment/notification";

    // Default status url path
    const STATUS_URL_PATH = "kredivo/payment/status";

    // Default success_url path
    const CHECKOUT_SUCCESS_URL_PATH = "checkout/onepage/success";

    // Default failure url path
    const CHECKOUT_FAILURE_URL_PATH = "checkout/onepage/failure";

    // Use secure url
    const USE_SECURE_URL = false;

    protected $_url;

    /**
     * Data constructor.
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        parent::__construct($context);
        $this->_url = $context->getUrlBuilder();
    }

    /**
     * @param $field
     * @return mixed
     */
    protected function getConfig($field)
    {
        $path = 'payment/' . $this->getCode() . '/' . $field;
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    protected function getCode()
    {
        return \Kredivo\Payment\Model\KredivoPayment::CODE;
    }

    /**
     * @return mixed
     */
    public function getServerKey()
    {
        return $this->getConfig('server_key');
    }

    /**
     * @return bool
     */
    public function getEnvironment()
    {
        return $this->getConfig('environment') == self::ENV ? true : false;
    }

    /**
     * @return mixed
     */
    public function getConversionRate()
    {
        return $this->getConfig('conversion_rate');
    }

    /**
     * @return mixed
     */
    public function getOrderStatus()
    {
        return $this->getConfig('order_status');
    }

    /**
     * @return string
     */
    public function getResponseUrl()
    {
        return $this->_url->getUrl(self::RESPONSE_URL_PATH, ['_secure' => self::USE_SECURE_URL]);
    }

    /**
     * @return string
     */
    public function getNotificationUrl()
    {
        return $this->_url->getUrl(self::NOTIFICATION_URL_PATH, ['_secure' => self::USE_SECURE_URL]);
    }

    /**
     * @return string
     */
    public function getStatusUrl()
    {
        return $this->_url->getUrl(self::STATUS_URL_PATH, ['_secure' => self::USE_SECURE_URL]);
    }

    /**
     * @return string
     */
    public function getCheckoutSuccessUrl()
    {
        return $this->_url->getUrl(self::CHECKOUT_SUCCESS_URL_PATH, ['_secure' => self::USE_SECURE_URL]);
    }

    /**
     * @return string
     */
    public function getCheckoutFailureUrl()
    {
        return $this->_url->getUrl(self::CHECKOUT_FAILURE_URL_PATH, ['_secure' => self::USE_SECURE_URL]);
    }
}