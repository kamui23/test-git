<!--
/**
 * Copyright © 2017 Icube, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
-->
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader',
        'Icube_Vtweb/js/action/set-payment-method',
        'Magento_Checkout/js/model/payment/additional-validators',
        'mage/url'
    ],
    function (Component, $, quote, urlBuilder, storage, errorProcessor, customer, fullScreenLoader, setPaymentMethodAction, additionalValidators, url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Icube_Vtweb/payment/vtweb'
            },
            redirectAfterPlaceOrder: false,
            /** Redirect to VT-Web */
            continueToPayPal: function () {
                if (additionalValidators.validate()) {
                    //update payment method information if additional data was changed
                    this.selectPaymentMethod();
                    setPaymentMethodAction(this.messageContainer);
                    return false;
                }
            },
            afterPlaceOrder: function () {
                window.location.replace(url.build('vtweb/payment/redirect'));
            }
        });
    }
);
