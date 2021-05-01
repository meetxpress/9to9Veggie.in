<?php

return [
    /*
     *--------------------------------------------------------------------------
     * Managed WooCommerce General Settings
     *--------------------------------------------------------------------------
     *
     * The following configuration items are general settings or high level
     * configurations for Managed WooCommerce
     *
     */

    /*
     * Managed WooCommerce Plugin URL
     */
    'url' => defined('MWC_CORE_PLUGIN_URL') ? MWC_CORE_PLUGIN_URL : null,

    /*
     * Managed WooCommerce Version
     */
    'version' => defined('MWC_CORE_VERSION') ? MWC_CORE_VERSION : null,

    /*
     *--------------------------------------------------------------------------
     * Managed WooCommerce Client
     *--------------------------------------------------------------------------
     *
     * The below information stores values related to the client side of MWC.
     * See https://github.com/gdcorp-partners/mwc-admin-client for more details
     *
     */
    'client' => [
	    'runtime' => [
		    'url' => 'https://cdn4.mwc.secureserver.net/runtime.js',
	    ],
	    'vendors' => [
		    'url' => 'https://cdn4.mwc.secureserver.net/vendors.js',
	    ],
	    'index' => [
		    'url' => 'https://cdn4.mwc.secureserver.net/index.js',
	    ],
    ],

    /*
     *--------------------------------------------------------------------------
     * MWC Local Assets
     *--------------------------------------------------------------------------
     *
     * Base directory locations for assets stored locally
     *
     */
    'assets' => [
        'styles' => defined('MWC_CORE_PLUGIN_DIR') ? MWC_CORE_PLUGIN_URL.'assets/css/' : '',
    ],
];
