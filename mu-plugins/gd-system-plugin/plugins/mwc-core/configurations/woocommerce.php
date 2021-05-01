<?php

return [
    /*
     *--------------------------------------------------------------------------
     * Configuration Flags
     *--------------------------------------------------------------------------
     *
     * disable marketplace suggestions flag, string.
     *
     */
    'flags' => [
        'disableMarketplaceSuggestions'                => get_option('gd_mwc_disable_woocommerce_marketplace_suggestions', 'yes'),
        'maybeFireLocalPickupShippingMethodAddedEvent' => get_option('gd_mwc_maybe_fire_local_pickup_shipping_method_added_event', 'yes'),
    ],
];
