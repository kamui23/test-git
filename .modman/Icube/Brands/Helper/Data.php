<?php

namespace Icube\Brands\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $moduleManager;
    protected $_httpContext;
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->moduleManager = $moduleManager;
        $this->_httpContext = $httpContext;
        $this->_scopeConfig = $scopeConfig;
    }

    public function getConfig()
    {
        $value = $this->_scopeConfig->getValue(
            'icube_brands/config/attribute_name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $value;
    }

    public function isLoggedIn()
    {
        return $this->_httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function isEnable()
    {
        return $this->moduleManager->isOutputEnabled('Kemana_AdvancedInventory');
    }
}
