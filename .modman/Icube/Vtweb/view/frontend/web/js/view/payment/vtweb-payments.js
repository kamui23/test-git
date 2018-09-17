<!--
/**
 * Copyright © 2017 Icube, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
-->
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
        ) {
        'use strict';
        rendererList.push(
            {
                type: 'vtweb',
                component: 'Icube_Vtweb/js/view/payment/method-renderer/vtweb-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
