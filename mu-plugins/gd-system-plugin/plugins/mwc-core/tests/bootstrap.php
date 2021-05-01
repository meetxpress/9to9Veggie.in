<?php

define('ABSPATH', 'foo/bar');

require_once dirname(__DIR__).'/vendor/autoload.php';

/*
 * Can't enable strict mode until we figure out how to set expectations for actions
 * and filters added in class constructors.
 *
 * We need to pass the callback to WP_Mock::expectActionAdded() using the instance
 * and the name of the method, but we don't get the instance until after the
 * constructor is executed.
 */
WP_Mock::setUsePatchwork(true);
// WP_Mock::activateStrictMode();
WP_Mock::bootstrap();

// see vendor/10up/wp_mock/php/WP_Mock/API/constant-mocks.php for constants
// defined by WP_Mock

if (! defined('WP_PLUGIN_DIR')) {
    define('WP_PLUGIN_DIR', '/dummy-files/wp-content/plugins');
}

if (! defined('WP_DEBUG')) {
    define('WP_DEBUG', false);
}

if (! defined('MINUTE_IN_SECONDS')) {
    define('MINUTE_IN_SECONDS', 60);
}

if (! defined('HOUR_IN_SECONDS')) {
    define('HOUR_IN_SECONDS', 3600);
}
