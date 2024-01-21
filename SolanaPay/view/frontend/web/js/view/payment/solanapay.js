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
                type: 'solanapay',
                component: 'ArmMage_SolanaPay/js/view/payment/method-renderer/solanapay-method'
            }
        );
        return Component.extend({});
    }
);