<?php

namespace Icube\Brands\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $moduleManager;

    public function __construct(\Magento\Framework\Module\Manager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    public function getConfig()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $scopeConfig = $om->create('Magento\Framework\App\Config\ScopeConfigInterface');
        $value = $scopeConfig->getValue(
            'icube_brands/config/attribute_name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $value;
    }

    public function isLoggedIn()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $objectManager->get('Magento\Framework\App\Http\Context');
        return $context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function isEnable()
    {
        return $this->moduleManager->isOutputEnabled('Kemana_AdvancedInventory');
    }
}
