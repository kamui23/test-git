<?php

namespace Kemana\Core\Model\Wyomind\System\Config\Source;

class Statuses extends \Wyomind\AdvancedInventory\Model\System\Config\Source\Statuses
{
    protected $_objectManager;

    public function __construct(\Magento\Framework\App\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    public function toOptionArray()
    {
        $orderConfig = $this->_objectManager->get("\Magento\Sales\Model\Order\Config");
        $alreadyProcessed = [];
        $data = [];
        foreach ($orderConfig->getStates() as $key => $state) {
            foreach ($orderConfig->getStateStatuses($key) as $key => $state) {
                if (!in_array($key, $alreadyProcessed)) {
                    $alreadyProcessed[] = $key;
                    $text = "";
                    $text = $this->getStateTxt($state);
                    $data[] = ['value' => $key, 'label' => $text];
                }
            }
        }

        return $data;
    }

    protected function getStateTxt($state) {
        if (is_string($state)) {
            return $state;
        }
        return $state->getText();
    }
}