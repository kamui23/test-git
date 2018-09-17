<?php

namespace Kemana\AdvancedInventory\Plugin\Model\Quote;

class Item
{
    public function beforeSetQty(\Magento\Quote\Model\Quote\Item $subject, $qty)
    {
        if (!$subject->getParentItem() || !$subject->getId()) {
            if ($subject->getQty()) {
                $qty = $qty - $subject->getQty();
                return [$qty];
            }
        }
        return [$qty];
    }
}