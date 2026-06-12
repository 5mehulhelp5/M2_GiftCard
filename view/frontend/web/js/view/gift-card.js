define([
    'ko',
    'Magento_Checkout/js/model/totals',
    'uiComponent',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/full-screen-loader',
    'mage/storage',
    'Market_GiftCard/js/resource-url-manager',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/error-processor',
], function (
    ko,
    totals,
    Component,
    stepNavigator,
    quote,
    fullScreenLoader,
    storage,
    resourceUrlManager,
    paymentService,
    errorProcessor,
) {
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
            fullScreenLoader.startLoader();

            return storage.post(
                resourceUrlManager.getUrlForGiftCardApplication(quote),
                JSON.stringify({
                    gift_card_code: this.code
                })
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
                    fullScreenLoader.stopLoader();
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                    fullScreenLoader.stopLoader();
                }
            );
        }

    });
});
