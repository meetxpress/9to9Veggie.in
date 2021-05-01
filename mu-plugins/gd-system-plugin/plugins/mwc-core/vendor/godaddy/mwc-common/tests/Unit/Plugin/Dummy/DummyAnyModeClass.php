<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Plugin\Dummy;

class DummyAnyModeClass
{
    static $loadCount = 0;

    public function __construct()
    {
        self::$loadCount++;
    }
}
