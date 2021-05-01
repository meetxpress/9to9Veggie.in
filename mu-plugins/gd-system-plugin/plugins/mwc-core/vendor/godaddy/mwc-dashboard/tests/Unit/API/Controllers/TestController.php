<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Tests\Unit\API\Controllers;

use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AbstractController;

/**
 * Test controller class used in AbstractControllerTest.
 */
class TestController extends AbstractController
{
    public function registerRoutes()
    {
    }

    public function getItemSchema(): array
    {
        return [];
    }
}
