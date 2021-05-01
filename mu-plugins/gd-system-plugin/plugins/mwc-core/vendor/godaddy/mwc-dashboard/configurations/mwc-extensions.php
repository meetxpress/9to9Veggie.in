<?php

return [

    /*
     *--------------------------------------------------------------------------
     * MWC Extensions Assets
     *--------------------------------------------------------------------------
     *
     * Locations for extensions assets
     */
    'assets' => [
        'css' => [
            'admin' => [
                'url' => defined('MWC_DASHBOARD_PLUGIN_URL') ? MWC_DASHBOARD_PLUGIN_URL.'assets/css/extensions-admin.css' : '',
            ],
        ],
    ],

    /*
     * List of slugs for extensions that should be featured.
     */
    'featured' => [
        'woocommerce-checkout-field-editor'     => true,
        'woocommerce-customer-order-csv-export' => true,
        'woocommerce-product-addons'            => true,
    ],

    /*
     * Map of slugs and categories for managed extensions.
     */
    'categories' => [
        'woocommerce-advanced-notifications'          => 'Store Management',
        'woocommerce-amazon-s3-storage'               => 'Product Type',
        'woocommerce-gateway-authorize-net-cim'       => 'Payments',
        'woocommerce-subscriptions'                   => 'Product Type',
        'woocommerce-shipping-australia-post'         => 'Shipping',
        'automatewoo'                                 => 'Marketing and Messaging',
        'woocommerce-memberships'                     => 'Product Type',
        'woocommerce-shipping-canada-post'            => 'Shipping',
        'woocommerce-cart-add-ons'                    => 'Cart and Checkout',
        'woocommerce-checkout-field-editor'           => 'Cart and Checkout',
        'woocommerce-shipping-fedex'                  => 'Shipping',
        'woocommerce-follow-up-emails'                => 'Marketing and Messaging',
        'woocommerce-pip'                             => 'Shipping',
        'woocommerce-customer-order-csv-export'       => 'Store Management',
        'woocommerce-shipment-tracking'               => 'Shipping',
        'woocommerce-bookings'                        => 'Product Type',
        'woocommerce-force-sells'                     => 'Cart and Checkout',
        'woocommerce-min-max-quantities'              => 'Cart and Checkout',
        'woocommerce-gateway-paypal-express-checkout' => 'Payments',
        'woocommerce-product-addons'                  => 'Merchandizing',
        'woocommerce-social-login'                    => 'Cart and Checkout',
        'woocommerce-checkout-add-ons'                => 'Cart and Checkout',
        'woocommerce-product-csv-import-suite'        => 'Store Management',
        'woocommerce-product-enquiry-form'            => 'Merchandizing',
        'woocommerce-google-analytics-pro'            => 'Marketing and Messaging',
        'woocommerce-order-status-manager'            => 'Store Management',
        'woocommerce-tab-manager'                     => 'Merchandizing',
        'woocommerce-sequential-order-numbers-pro'    => 'Store Management',
        'woocommerce-product-vendors'                 => 'Product Type',
        'woocommerce-subscription-downloads'          => 'Product Type',
        'woocommerce-order-status-control'            => 'Store Management',
        'woocommerce-warranty'                        => 'Store Management',
        'woocommerce-shipping-royalmail'              => 'Shipping',
        'woocommerce-cost-of-goods'                   => 'Store Management',
        'woocommerce-shipping-multiple-addresses'     => 'Shipping',
        'woocommerce-software-add-on'                 => 'Product Type',
        'woocommerce-square'                          => 'Payments',
        'woocommerce-gateway-intuit-qbms'             => 'Payments',
        'woocommerce-gateway-elavon'                  => 'Payments',
        'woocommerce-shipping-local-pickup-plus'      => 'Shipping',
        'woocommerce-gateway-stripe'                  => 'Payments',
        'woocommerce-bulk-stock-management'           => 'Store Management',
        'woocommerce-url-coupons'                     => 'Marketing and Messaging',
        'woocommerce-table-rate-shipping'             => 'Shipping',
        'woocommerce-shipping-ups'                    => 'Shipping',
        'woocommerce-cart-notices'                    => 'Cart and Checkout',
        'woocommerce-shipping-usps'                   => 'Shipping',
        'woocommerce-360-image'                       => 'Merchandizing',
        'woocommerce-gateway-moneris'                 => 'Payments',
        'woocommerce-accommodation-bookings'          => 'Product Type',
        'woocommerce-additional-variation-images'     => 'Merchandizing',
        'woocommerce-box-office'                      => 'Product Type',
        'woocommerce-brands'                          => 'Merchandizing',
        'woocommerce-deposits'                        => 'Payments',
        'woocommerce-email-customizer'                => 'Marketing and Messaging',
        'woocommerce-order-barcodes'                  => 'Product Type',
        'woocommerce-payments'                        => 'Payments',
        'woocommerce-photography'                     => 'Product Type',
        'woocommerce-points-and-rewards'              => 'Marketing and Messaging',
        'woocommerce-pre-orders'                      => 'Product Type',
        'woocommerce-products-compare'                => 'Merchandizing',
        'woocommerce-bookings-availability'           => 'Product Type',
        'woocommerce-gateway-purchase-order'          => 'Payments',
        'woocommerce-quick-view'                      => 'Merchandizing',
        'woocommerce-store-catalog-pdf-download'      => 'Merchandizing',
        'woocommerce-admin-custom-order-fields'       => 'Store Management',
        'woocommerce-customer-order-csv-import'       => 'Store Management',
        'woocommerce-gateway-beanstream'              => 'Payments',
        'woocommerce-gateway-chase-paymentech'        => 'Payments',
        'woocommerce-gateway-cybersource'             => 'Payments',
        'woocommerce-gateway-realex-redirect'         => 'Payments',
        'woocommerce-memberships-mailchimp'           => 'Marketing and Messaging',
        'woocommerce-memberships-for-teams'           => 'Product Type',
        'woocommerce-nested-category-layout'          => 'Merchandizing',
        'woocommerce-pdf-product-vouchers'            => 'Product Type',
        'woocommerce-address-validation'              => 'Cart and Checkout',
        'woocommerce-product-documents'               => 'Merchandizing',
        'woocommerce-product-reviews-pro'             => 'Merchandizing',
        'woocommerce-twilio-sms-notifications'        => 'Marketing and Messaging',
        'woocommerce-xero'                            => 'Store Management',
    ],
];
