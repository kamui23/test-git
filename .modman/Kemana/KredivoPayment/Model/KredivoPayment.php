<?php

namespace Kemana\KredivoPayment\Model;

/**
 * Class KredivoPayment
 * @package Kemana\KredivoPayment\Model
 */
class KredivoPayment extends \Kredivo\Payment\Model\KredivoPayment
{
    protected $_isInitializeNeeded = true;

    /**
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

    /**
     * @param string $paymentAction
     * @param object $stateObject
     * @return \Kredivo\Payment\Model\KredivoPayment|void
     */

    public function initialize($paymentAction, $stateObject)
    {
        $state = $this->getConfigData('order_status');
        $stateObject->setStatus($state);
    }
}