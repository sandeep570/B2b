/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/quote'
], function(ko, Component, selectShippingAddressAction, quote) {
    'use strict';
    return Component.extend({
        defaults: {
            template: '%path to your template%'
        },

        initProperties: function () {
            this._super();
            this.isSelected = ko.computed(function() {
                var isSelected = false;
                var shippingAddress = quote.shippingAddress();
                if (shippingAddress) {
                    isSelected = shippingAddress.getKey() == this.address().getKey();
                }
                return isSelected;
            }, this);

            return this;
        },

        /** Set selected customer shipping address  */
        selectAddress: function() {
            selectShippingAddressAction(this.address());
        },

        /** additional logic required for this renderer  **/

    });
});