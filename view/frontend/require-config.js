var config = {
    map: {
        '*': {
            orderApprove: 'Cminds_MultiUserAccounts/js/checkout/order-approve',
            'Magento_Checkout/js/view/shipping':'Cminds_MultiUserAccounts/js/view/shipping-methods',
        }
    },
    deps: [
        "Magento_Checkout/js/checkout-loader"
    ]
};