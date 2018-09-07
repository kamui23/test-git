/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    "jquery",
    'mage/template',
    'jquery/ui',
    'mage/validation',
    'Magento_Checkout/js/checkout-data',
], function(
        $,
        mage,
        ui,
        validation,
        checkoutData
    ){
    "use strict";

    $.widget('mage.storepickup', {
        options: {
            orderAttr : 'shippingAddress.custom_attributes.amorderattr_store_code',
            ajaxStore : '/icubeorder/item/storeinfo',
            countryId : 'cityCopy'
        },
        _create: function () {

            var checkInterval;
            var that = this;

            checkInterval = setInterval(function () {
                var loaderContainer = $('div[name="' + that.options.orderAttr + '"]');

                //Return if loader still load
                if (loaderContainer.length == 0) {
                    return;
                }

                //Remove loader and clear update interval if content loaded
                if (loaderContainer.length > 0 ) {
                    clearInterval(checkInterval);

                    var isStorepickup = localStorage.getItem('selected-store-pickup');
                    if(isStorepickup) {
                        var accessPage = localStorage.getItem('intAccessPage');
                        if(typeof accessPage === 'undefined') {
                            localStorage.setItem('intAccessPage', 0);
                        }
                        $('.form-shipping-address').hide();
                        that.getStoreInfo(isStorepickup);
                        that.setupStorePickupBilling();
                        $('.form-shipping-address').show();

                        $('#checkout-step-shipping').css('border-bottom','1px solid #dedede');
                        /*$('.checkout-shipping-method').hide();*/
                    } else {
                        $('div[name="shippingAddress.lastname"] input').val(' ');
                        $('div[name="shippingAddress.street.0"] input').val(' ');

                        $('#checkout-step-shipping').css('border-bottom','none');
                        $('.checkout-shipping-method').show();
                    }
                }

            }, 100);

        },
        getStoreInfo: function(storeCode) {
            var that = this,
                selectedStore = storeCode;

            $.ajax({
                method: "POST",
                url: that.options.ajaxStore,
                data: { place_id: selectedStore}
            }).success(function(response) {
                var data = $.parseJSON(response);

                // set storepickup code
                $('div[name="shippingAddress.custom_attributes.amorderattr_store_code"] input').val(data.store_code).attr('disabled', true);

                // save storepickup information
                localStorage.setItem('store_name',data.store_name);
                localStorage.setItem('store_street',data.street1);
                localStorage.setItem('store_city',data.city);
                localStorage.setItem('store_zip',data.zip);
                localStorage.setItem('store_phone',data.phone);
                localStorage.setItem('region_name',data.region_name);
            });
        },
        setupStorePickupBilling: function() {
            var that = this;

            // show "Catatan" field
            $('div[name="shippingAddress.custom_attributes.amorderattr_catatan"]').show();

            if($('.shipping-address-items').length > 0) {
                // change label "Nama Pengambil" to "Nama Depan"
                $('div[name="shippingAddress.firstname"] label span').text('Nama Depan');
                // add checkbox for "Informasi Pengambil sama dengan Data Billing"
                that.addFieldPickup();
            } else {
                // change label "Nama Depan" to "Nama Pengambil"
                $('div[name="shippingAddress.firstname"] label span').text('Nama Pengambil');
                // Set form value billing for guest with store address
                that.setBillingWithStoreAddress();
            }
            that.getPickupPersonInfo();
        },
        getPickupPersonInfo: function() {
            var that = this;
            var checkInterval;

            checkInterval = setInterval(function () {
                var loaderProcess = $('._keyfocus');
              
                if (loaderProcess.length == 0) {
                    //remove interval clear function to always listen to customer input when typing on for First + Last Name + Phone# field
                    if($('.customer-address-list.name').length > 0) {
                        // user loggedIn
                        var name = $('.shipping-address-item.selected-item').find('.customer-address-list.name').val();
                        var phone = $('.shipping-address-item.selected-item').find('.customer-address-list.telephone').val();
                        clearInterval(checkInterval);
                    } else {
                        // as guest
                        var name = $('div[name="shippingAddress.firstname"] input').val();
                        var phone = $('div[name="shippingAddress.telephone"] input').val();
                        $('div[name="shippingAddress.lastname"] input').val('-').trigger('change');
                    }

                    that.dataPickup(name, phone);
                }
              
                //Return if loader exist
                if (loaderProcess.length > 0 ) {
                    return;    
                }
            },100);
        },
        dataPickup: function(name, phone){
            if($('.customer-address-list.name').length > 0) {
                if($('#isSameperson').is(':checked')){
                    $('div[name="shippingAddress.custom_attributes.amorderattr_nama_pengambil"] input').val(name).trigger('change');
                    $('div[name="shippingAddress.custom_attributes.amorderattr_nomor_pengambil"] input').val(phone).trigger('change');
                }
            } else {
                $('div[name="shippingAddress.custom_attributes.amorderattr_nama_pengambil"] input').val(name).prop('disabled', true).addClass('input-disabled').trigger('change');
                $('div[name="shippingAddress.custom_attributes.amorderattr_nomor_pengambil"] input').val(phone).prop('disabled', true).addClass('input-disabled').trigger('change');
            }
        },
        setBillingWithStoreAddress: function() {
            var that = this,
                checkInterval;

            checkInterval = setInterval(function () {                
                var loaderContainer = $('select[name="' + that.options.countryId + '"]');

                //Return if loader still load
                if (loaderContainer.length == 0) {
                    return;
                }

                if (loaderContainer.length > 0 ) {
                    // set street
                    $('div[name="shippingAddress.street.0"] input').val('--storepickup--').trigger('change');
                    // set state
                    $('div[name="shippingAddress.region_id"] select').val('517').trigger('change');
                    $('div[name="shippingAddress.region"] input').val('DKI JAKARTA');
                    // set city/kecamatan from electronicsolutions/app/design/frontend/Icube/es/Icube_City/web/js/city-kecamatan.js - line 76

                    // hide other field that's not needed for store pickup
                    $('div[name="shippingAddress.lastname"], fieldset.field.street, div[name="shippingAddress.country_id"], div[name="shippingAddress.region_id"], div[name="shippingAddress.city"], div[name="shippingAddress.postcode"]').hide();
                    clearInterval(checkInterval);
                }
            },100);

        },
        addFieldPickup: function() {
            var that = this;
            var checkInterval;

            checkInterval = setInterval(function () {
                var loaderProcess = $('.customer-address-list.name');
              
                if (loaderProcess.length == 0) {
                    //Return if loader is not there yet
                    return;
                }
              
                //If loader found execute these code :
                if (loaderProcess.length > 0 ) {
                    $('div[name="shippingAddress.custom_attributes.amorderattr_nama_pengambil"]').show();
                    $('div[name="shippingAddress.custom_attributes.amorderattr_nomor_pengambil"]').show();
                    $('div[name="shippingAddress.custom_attributes.amorderattr_nama_pengambil"]').before('<div class="pickup-information"><div class="pickup-option"><input type="checkbox" id="isSameperson" name="pickup-id"/><label class="label" for="isSameperson">Sama dengan data pembeli</label></div></div>');                    
                    clearInterval(checkInterval);
                    that.pickupPersonSameAsBilledPerson();
                    $('#isSameperson').trigger('click');
                }
            },350);
        },
        pickupPersonSameAsBilledPerson: function() {
            var that = this;
            
            if($('#isSameperson').is(':checked')){
                that.disabledPickupInfo();
            } else {
                that.destroyPickupInfo();
            }

            $('#isSameperson').click(function(){
                if($(this).prop("checked") == true){
                    var name = $('.shipping-address-item.selected-item').find('.customer-address-list.name').val();
                    var phone = $('.shipping-address-item.selected-item').find('.customer-address-list.telephone').val();
                    that.dataPickup(name, phone);
                    
                    that.disabledPickupInfo();
                } else {
                    that.destroyPickupInfo();
                }
            });
        },
        disabledPickupInfo: function() {
            $('div[name="shippingAddress.custom_attributes.amorderattr_nama_pengambil"] input').prop('disabled', true).addClass('input-disabled');
            $('div[name="shippingAddress.custom_attributes.amorderattr_nomor_pengambil"] input').prop('disabled', true).addClass('input-disabled');
        },
        destroyPickupInfo: function() {
            $('div[name="shippingAddress.custom_attributes.amorderattr_nama_pengambil"] input').val(' ').prop('disabled', false).removeClass('input-disabled');
            $('div[name="shippingAddress.custom_attributes.amorderattr_nomor_pengambil"] input').val(' ').prop('disabled', false).removeClass('input-disabled');
        }
    });
    return $.mage.storepickup;

});