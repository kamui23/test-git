<?php

namespace Kemana\FixValidateQuoteAmount\Plugin\Model;

use Magento\Quote\Model\Quote as QuoteEntity;

class FixQuoteAmount
{
    public function aroundValidateQuoteAmount(\Magento\Quote\Model\QuoteValidator $subject, \Closure $proceed, QuoteEntity $quote, $amount)
    {

        $self = $proceed($quote, $amount);
        if ($amount >= \Magento\Quote\Model\QuoteValidator::MAXIMUM_AVAILABLE_NUMBER && (count($quote->getErrors()) == 1)) {
            $quote->setHasError(false);
        }
        return $self;
    }
}
