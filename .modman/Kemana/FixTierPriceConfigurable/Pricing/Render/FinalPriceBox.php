<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kemana\FixTierPriceConfigurable\Pricing\Render;

/**
 * Class for final_price rendering
 *
 * @method bool getUseLinkForAsLowAs()
 * @method bool getDisplayMinimalPrice()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    protected $_moduleManager;

    /**
     * Check is MSRP applicable for the current product.
     *
     * @return bool
     */
    protected function isMsrpPriceApplicable()
    {
        $_moduleManager = $this->getModuleManager();

        if (!$_moduleManager->isEnabled('Magento_Msrp') || !$_moduleManager->isOutputEnabled('Magento_Msrp')) {
            return false;
        }

        try {
            $msrpPriceType = $this->getSaleableItem()->getPriceInfo()->getPrice('msrp_price');
        } catch (\InvalidArgumentException $e) {
            $this->_logger->critical($e);
            return false;
        }

        if ($msrpPriceType === null) {
            return false;
        }

        $product = $this->getSaleableItem();

        return $msrpPriceType->canApplyMsrp($product) && $msrpPriceType->isMinimalPriceLessMsrp($product);
    }

    /**
     * @return \Magento\Framework\Module\Manager|mixed
     */
    private function getModuleManager()
    {
        if ($this->_moduleManager === null) {
            $this->_moduleManager = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\Module\Manager::class);
        }
        return $this->_moduleManager;
    }
}