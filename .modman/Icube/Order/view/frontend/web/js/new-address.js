/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
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
        'use strict';

        return {
            /**
             * Get shipping rates for specified address.
             * @param {Object} address
             */
            getRates: function (address) {
                shippingService.isLoading(true);
                var cache = rateRegistry.get(address.getCacheKey()),
                    serviceUrl = resourceUrlManager.getUrlForEstimationShippingMethodsForNewAddress(quote),
                    payload = JSON.stringify({
                            address: {
                                'street': address.street,
                                'city': address.city,
                                'region_id': address.regionId,
                                'region': address.region,
                                'country_id': address.countryId,
                                'postcode': address.postcode,
                                'email': address.email,
                                'customer_id': address.customerId,
                                'firstname': address.firstname,
                                'lastname': address.lastname,
                                'middlename': address.middlename,
                                'prefix': address.prefix,
                                'suffix': address.suffix,
                                'vat_id': address.vatId,
                                'company': address.company,
                                'telephone': address.telephone,
                                'fax': address.fax,
                                'custom_attributes': address.customAttributes,
                                'save_in_address_book': address.saveInAddressBook
                            }
                        }
                    );

                if (cache) {
                    shippingService.setShippingRates(cache);
                    shippingService.isLoading(false);
                } else {
                    storage.post(
                        serviceUrl, payload, false
                    ).done(
                        function (result) {
                            rateRegistry.set(address.getCacheKey(), result);
                            shippingService.setShippingRates(result);

                            var isStorepickup = localStorage.getItem('selected-store-pickup');
                            if(isStorepickup){
                                $('.onestepcheckout-index-index .opc.one-step-checkout-container #checkout-shipping-method-load .row').addClass('hide');
                                $.ajax({
                                    method: "GET",
                                    url: "/icubeorder/item/storeinfo",

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
                        function (response) {
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
