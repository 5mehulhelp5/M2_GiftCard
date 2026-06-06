define([
    'ko',
    'Magento_Checkout/js/model/totals',
    'uiComponent',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/quote'
], function (ko, totals, Component, stepNavigator, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Market_GiftCard/checkout/gift-card',
            track: {
                code: 1,
                isApplied: 1
            }
        },

        code: '',
        isApplied: false,

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
        },

        update: function (){

        }

    });
});
