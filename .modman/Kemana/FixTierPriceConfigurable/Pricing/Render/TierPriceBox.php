<?php

namespace Kemana\FixTierPriceConfigurable\Pricing\Render;

/**
 * Responsible for displaying tier price box on configurable product page.
 *
 * @package Kemana\FixTierPriceConfigurable\Pricing\Render
 */
class TierPriceBox extends FinalPriceBox
{
    /**
     * @inheritdoc
     */
    public function toHtml()
    {
        // Hide tier price block in case of MSRP.
        if (!$this->isMsrpPriceApplicable()) {
            return parent::toHtml();
        }
    }
}