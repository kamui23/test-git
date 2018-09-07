<?php
/**
 *
 * Copyright © 2017 Icube, Inc. All rights reserved.
 * See COPYING.txt for details.
 */

namespace Icube\Vtweb\Model;

class Standard extends \Magento\Payment\Model\Method\AbstractMethod
{
    const TRX_STATUS_SETTLEMENT = 'settlement';
    const ORDER_STATUS_EXPIRE   = 'expire';
    protected $_code = 'vtweb';

    protected $_isInitializeNeeded     = true;
    protected $_canUseInternal         = true;
    protected $_canUseForMultishipping = false;

    protected $_formBlockType = 'vtweb/form';
    protected $_infoBlockType = 'vtweb/info';

    // call to redirectAction function at Veritrans_Vtweb_PaymentController
    public function getOrderPlaceRedirectUrl()
    {
        return 'http://www.google.com/';
    }
}

?>