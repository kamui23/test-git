<?php

namespace Icube\Brands\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $moduleManager;

    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\ObjectManager $objectManager
    ) {
        $this->moduleManager = $moduleManager;
        $this->_objectManager = $objectManager;
    }

    public function getConfig()
    {
        $scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $scopeConfig->getValue(
            'icube_brands/config/attribute_name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $value;
    }

    public function isLoggedIn()
    {
        $context = $this->_objectManager->get('Magento\Framework\App\Http\Context');
        return $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function isEnable()
    {
        return $this->moduleManager->isOutputEnabled('Kemana_AdvancedInventory');
    }
}
