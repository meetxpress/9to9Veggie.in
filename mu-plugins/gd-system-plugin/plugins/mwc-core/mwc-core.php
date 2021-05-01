<?php
/**
 * Plugin name: MWC Core
 */

namespace GoDaddy\WordPress\MWC\Core;

use Exception;

require_once __DIR__.'/vendor/autoload.php';

// instantiate the core package
try {
    Package::getInstance();
} catch (Exception $exception) {
    // TODO: log the exception when a custom logger is added {CW 2021-02-22}
}
