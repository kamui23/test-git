/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    'mage/template',
    'jquery/ui',
    'mage/validation',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote'
], function(
        $,
        mage,
        ui,
        validation,
        selectShippingAddressAction,
        checkoutData,
        quote
    ){
    "use strict";
    var checkoutajaxurl = $('#checkout_ajaxurl').text();
    var checkout_ajaxurl = checkoutajaxurl.replace(/\s/g, '');
    
    $.widget('mage.storepickup', {
        options: {
            orderAttr : 'shippingAddress.custom_attributes.amorderattr_store_code',
            ajaxStore : checkout_ajaxurl,
            countryId : 'cityCopy'
        },
        _create: function () {

            var checkInterval;
            var that = this;
            var isStorepickup = localStorage.getItem('selected-store-pickup');
            console.log(isStorepickup);
            if(isStorepickup){

            checkInterval = setInterval(function () {
                var loaderContainer = $('.shipping-address-items .shipping-address-item');
                var loaderSecondary = $('#checkout-step-shipping > .form.form-shipping-address');

                //Return if loader still load
                if (loaderContainer.length == 0 && loaderSecondary.length == 0 ) {
                    return;
                }

                //Remove loader and clear update interval if content loaded
                if (loaderContainer.length > 0 || loaderSecondary.length > 0) {

                    clearInterval(checkInterval);

                    // if customer address has been found
                    if(loaderContainer.length > 0){    
                        $.ajax({
                            method: "GET",
                            url: that.options.ajaxStore,
                            
                            beforeSend: function() {
                                $('#shipping-block').append('<div class="loader-wrapper"> </div>'); 
                                $('.shipping-information .ship-to').append('<div class="loader-wrapper"> </div>');  
                                           
                            },
                            complete: function() {                         
                                $('#shipping-block .loader-wrapper').remove();
                                $('.shipping-information .ship-to .loader-wrapper').remove();

                            },
                            success: function(response) {
                                $('.opc-wrapper .addresses .shipping-address-items .shipping-address-item').addClass('hide'); 
                                
                                //if store pickup
                                if(response.type == 'pickup'){
                                    // replace the 'selected' customer address with storepickup date
                                    var storepickupinfo = '-';
                                    storepickupinfo = response.store_name + '<br/>' + response.street1 + '<br/>' + response.city + ', ' + response.region_name;      
                                    $('.shipping-address-item.selected-item').html(storepickupinfo);
                                    $('.shipping-address-item.selected-item').removeClass('hide');

                                    // replace the 'ship-to' field with storepickup's data
                                    $('.ship-to .shipping-information-content').html(storepickupinfo);
                                    
                                    // disabled add new address
                                    $('#checkout-step-shipping .action.action-show-popup').remove();

                                    // set store shipping address to quote
                                    var shippingAddress = quote.shippingAddress();

                                        shippingAddress.street = new Array(
                                                response.street1,
                                                response.street2
                                                );
                                        shippingAddress.city = response.city;
                                        shippingAddress.postcode = response.zip;
                                        shippingAddress.region = response.region_name;
                                }

                                // if delivery to home or mixed
                                else{
                                    if(response.type == 'mixed'){
                                        var deliveryNotif = '<div class="delivery-notif">Some of item(s) are not available in the selected store, we will deliver it to your address</div>';   
                                        var itemPickedUp = [];

                                        itemPickedUp = response.pickup_id;
                                        $.each(itemPickedUp, function(index, value) {
                                            $('#pickup-note-'+value+' .status').text('(store pickup)');
                                        }); 
                                    }
                                    else{
                                        var deliveryNotif = '<div class="delivery-notif">Sorry, your item(s) are not available in the selected store. We will deliver it to your address</div>';   
                                    }

                                    $('#checkout-step-shipping .field.addresses').prepend(deliveryNotif);

                                    $('.shipping-address-items .shipping-address-item').removeClass('hide');
                                    $('#checkout-step-shipping .action.action-show-popup').removeClass('hide');
                                }
                            }
                        });
                    }

                    // if data customer address is empty 
                    else{
                        console.log('doanh_doanh');
                         $.ajax({
                            method: "GET",
                            url: that.options.ajaxStore,
                            
                            beforeSend: function() {
                                $('#shipping-block').append('<div class="loader-wrapper"> </div>'); 
                                           
                            },
                            complete: function() {                        
                                $('#shipping-block .loader-wrapper').remove();  
                            },
                            success: function(response) {
                                console.log(response);
                                $("#shipping-new-address-form .field.street").addClass('hide');
                                $("#shipping-new-address-form .field[name='shippingAddress.country_id']").addClass('hide');
                                $("#shipping-new-address-form .field[name='shippingAddress.region_id']").addClass('hide');
                                $("#shipping-new-address-form .field[name='shippingAddress.city']").addClass('hide');
                                $("#shipping-new-address-form .field[name='shippingAddress.postcode']").addClass('hide'); 





                                //if store pickup
                                if(response.type == 'pickup'){             
                                    var country_id = window.checkoutConfig.originCountryCode;

                                    // enter store data to form
                                    $('#shipping-new-address-form div[name="shippingAddress.street.0"] input').val(response.street1).trigger('change');
                                    $('#shipping-new-address-form div[name="shippingAddress.street.1"] input').val(response.street2).trigger('change');
                                    $('[name="shippingAddress.country_id"] select').val(country_id).trigger('change');;
                                    $('#shipping-new-address-form div[name="shippingAddress.region_id"] select').val(response.region_id).trigger('change');
                                    $('#shipping-new-address-form div[name="shippingAddress.city"] select').remove();
                                    $('#shipping-new-address-form div[name="shippingAddress.city"] .input-text').val(response.city).trigger('change');
                                    $('#shipping-new-address-form div[name="shippingAddress.postcode"] input').val(response.zip).trigger('change');

                                   


                                    // show storeinfo as selected address
                                    var storepickupinfo = '-';
                                    storepickupinfo = '<div class="pickup-info">' + response.store_name + '<br/>' + response.street1 + '<br/>' + response.city + ', ' + response.region_name + '</div>';  
                                    $('form.form-shipping-address').append(storepickupinfo);

                                }
                                else {
                                    if(response.type == 'mixed'){
                                        var deliveryNotif = '<div class="delivery-notif">Some of item(s) not available in selected store, please provide delivery address</div>';
                                        var itemPickedUp = [];

                                        itemPickedUp = response.pickup_id;
                                        $.each(itemPickedUp, function(index, value) {
                                            $('#pickup-note-'+value+' .status').text('(store pickup)');
                                        });     
                                    }
                                    else{
                                        var deliveryNotif = '<div class="delivery-notif">Sorry, your item(s) are not available in the selected store. We will deliver it to your address.</div>';   
                                    }

                                    $('.form.form-shipping-address').prepend(deliveryNotif); 
                                    
                                    $("#shipping-new-address-form .field.street").removeClass('hide');
                                    $("#shipping-new-address-form .field[name='shippingAddress.country_id']").removeClass('hide');
                                    $("#shipping-new-address-form .field[name='shippingAddress.region_id']").removeClass('hide');
                                    $("#shipping-new-address-form .field[name='shippingAddress.city']").removeClass('hide');
                                    $("#shipping-new-address-form .field[name='shippingAddress.postcode']").removeClass('hide');
                                }
                            }
                        });
                    }

                }

            }, 100);    
            }  
        }
        
    });
    return $.mage.storepickup;

});