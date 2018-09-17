define([], function () {
    'use strict';

    return function (KredivoPaymentMethod) {
        return KredivoPaymentMethod.extend({
            defaults: {
                template: 'Kemana_KredivoPayment/payment/kredivopayment'
            },
            redirectAfterPlaceOrder: false,
            /**
             * Get value of instruction field.
             * @returns {String}
             */
            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            }
        });
    }
});