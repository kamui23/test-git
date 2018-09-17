<?php

namespace Kemana\FixStoreNotFound\Plugin\Mailchimp\Helper;

class Data
{
    const SCOPE_WEBSITE = 'website';

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $_context;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    )
    {
        $this->_context = $context;
        $this->_request = $context->getRequest();
    }

    public function beforeGetConfigValue(
        $subject,
        $path,
        $storeId = null,
        $scope = null
    )
    {
        $website = $this->getWebsiteParam();

        if (!is_null($website) && is_null($scope)) {
            $scope = self::SCOPE_WEBSITE;
        }

        return [$path, $storeId, $scope];
    }

    public function getWebsiteParam()
    {
        return $this->_request->getParam(self::SCOPE_WEBSITE, null);
    }
}