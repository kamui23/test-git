<?php

namespace Kemana\Core\Plugin\Amasty;

use Magento\Payment\Helper\Data;

class Payrestriction extends \Amasty\Payrestriction\Plugin\Payrestriction
{
    public function aroundGetStoreMethods(
        Data $subject,
        \Closure $proceed,
        $store = null,
        $quote = null)
    {
        $methods = $proceed($store, $quote);

        if (!$quote) {
            return $methods;
        }

        $quote->collectTotals();
        // $this->quoteRepository->save($quote); //ICUBE CUSTOM - commented out because caused error in checkout
        $quote->save(); //ICUBE CUSTOM - to subtitute the line above

        $address = $quote->getShippingAddress();

        $items = $quote->getAllItems();
        $address->setItemsToValidateRestrictions($items);

        $hasBackOrders = false;
        $hasNoBackOrders = false;
        foreach ($items as $item) {
            if ($item->getBackorders() > 0) {
                $hasBackOrders = true;
            } else {
                $hasNoBackOrders = true;
            }
            if ($hasBackOrders && $hasNoBackOrders) {
                break;
            }
        }

        foreach ($methods as $k => $method) {
            foreach ($this->getRules($address, $items) as $rule) {
                $validBackOrder = true;
                switch ($rule->getOutOfStock()) {
                    case \Amasty\Payrestriction\Model\Rule::BACKORDERS_ONLY:
                        if (($hasBackOrders && $hasNoBackOrders) || (!$hasBackOrders && $hasNoBackOrders)) {
                            $validBackOrder = false;
                        } elseif ($hasBackOrders) {
                            $validBackOrder = true;
                        }
                        break;
                    case \Amasty\Payrestriction\Model\Rule::NON_BACKORDERS:
                        $validBackOrder = $hasBackOrders ? false : true;
                        if (($hasBackOrders && $hasNoBackOrders) || ($hasBackOrders && !$hasNoBackOrders)) {
                            $validBackOrder = false;
                        } elseif ($hasNoBackOrders) {
                            $validBackOrder = true;
                        }
                        break;
                }

                if ($validBackOrder && $rule->restrict($method)) {
                    if ($rule->validate($address)
                    ) {
                        unset($methods[$k]);
                    }//if validate
                }//if restrict
            }//rules
        }//methods
        return $methods;
    }
}