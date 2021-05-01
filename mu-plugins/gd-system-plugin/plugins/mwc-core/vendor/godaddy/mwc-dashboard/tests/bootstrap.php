<?php

// first we need to load the composer autoloader so we can use WP Mock
require_once dirname(__DIR__).'/vendor/autoload.php';

// now bootstrap WP_Mock
WP_Mock::setUsePatchwork(true);
WP_Mock::activateStrictMode();
WP_Mock::bootstrap();
