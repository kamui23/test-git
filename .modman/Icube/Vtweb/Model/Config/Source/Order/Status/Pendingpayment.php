<?php
/**
 *
 * Copyright © 2017 Icube, Inc. All rights reserved.
 * See COPYING.txt for details.
 */

namespace Icube\Vtweb\Model\Config\Source\Order\Status;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Config\Source\Order\Status;

class Pendingpayment extends Status
{
    protected $_stateStatuses = [Order::STATE_PENDING_PAYMENT];
}
