/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/model/quote',
        'mage/storage',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/model/error-processor'
    ],
    function ($, resourceUrlManager, quote, storage, shippingService, rateRegistry, errorProcessor) {
        "use strict";
        return {
            getRates: function(address) {
                shippingService.isLoading(true);
                var cache = rateRegistry.get(address.getKey());
                if (cache) {
                    shippingService.setShippingRates(cache);
                    shippingService.isLoading(false);
                } else {
                    storage.post(
                        resourceUrlManager.getUrlForEstimationShippingMethodsByAddressId(),
                        JSON.stringify({
                            addressId: address.customerAddressId
                        }),
                        false
                    ).done(
                        function(result) {
                            rateRegistry.set(address.getKey(), result);
                            shippingService.setShippingRates(result);

                            var isStorepickup = localStorage.getItem('selected-store-pickup');
                            if(isStorepickup){
                                $('.onestepcheckout-index-index .opc.one-step-checkout-container #checkout-shipping-method-load .row').addClass('hide');
                                $.ajax({
                                    method: "GET",
                                    url: "/icubeorder/item/storeinfo",

                                    beforeSend: function() {
                                        $('#co-shipping-method-form').append('<div class="loader-wrapper"> </div>');     
                                    },
                                    complete: function() {                        
                                        $('#co-shipping-method-form .loader-wrapper').remove();  
                                    },

                                    success: function(response) {

                                        // $('.onestepcheckout-index-index .opc.one-step-checkout-container #checkout-shipping-method-load .row').addClass('hide');

                                        if(response.type == 'pickup'){
                                            $('#checkout-shipping-method-load #pickup').removeClass('hide');
                                            $('#checkout-shipping-method-load #pickup').click();
                                        }
                                        else{
                                            $('#checkout-shipping-method-load .row').removeClass('hide');
                                            $('#checkout-shipping-method-load #pickup').addClass('hide');   
                                        }
                                    }
                                });
                            }
                            else{
                                $('#checkout-shipping-method-load #pickup').addClass('hide'); 
                                $('#checkout-shipping-method-load .row:first-child').click(); 
                            }
                        }

                    ).fail(
                        function(response) {
                            shippingService.setShippingRates([]);
                            errorProcessor.process(response);
                        }
                    ).always(
                        function () {
                            shippingService.isLoading(false);
                        }
                    );
                }
            }
        };
    }
);
