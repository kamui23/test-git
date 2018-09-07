<?php

namespace Kemana\Core\Model\Wyomind\System\Config\Source;

class Statuses extends \Wyomind\AdvancedInventory\Model\System\Config\Source\Statuses
{
    public function toOptionArray()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $orderConfig = $om->get("\Magento\Sales\Model\Order\Config");
        $alreadyProcessed = [];
        $data = [];
        foreach ($orderConfig->getStates() as $key => $state) {
            foreach ($orderConfig->getStateStatuses($key) as $key => $state) {
                if (!in_array($key, $alreadyProcessed)) {
                    $alreadyProcessed[] = $key;
                    $text = "";
                    if (is_string($state)) {
                        $text = $state;
                    } else {
                        $text = $state->getText();
                    }
                    $data[] = ['value' => $key, 'label' => $text];
                }
            }
        }

        return $data;
    }
}