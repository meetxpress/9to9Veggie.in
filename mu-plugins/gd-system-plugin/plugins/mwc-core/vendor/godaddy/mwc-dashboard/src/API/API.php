<?php

namespace GoDaddy\WordPress\MWC\Dashboard\API;

use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\AccountController;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ExtensionsController;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\MessagesController;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\PluginsController;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\ShopController;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\SupportController;
use GoDaddy\WordPress\MWC\Dashboard\API\Controllers\UserController;

class API
{
    /**
     * All available API controllers.
     *
     * @var array
     */
    protected $controllers;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->setControllers();

        Register::action()
            ->setGroup('rest_api_init')
            ->setHandler([$this, 'registerRoutes'])
            ->execute();
    }

    /**
     * Registers all available API controllers.
     */
    protected function setControllers()
    {
        $this->controllers = [
            new AccountController(),
            new ExtensionsController(),
            new MessagesController(),
            new ShopController(),
            new PluginsController(),
            new SupportController(),
            new UserController(),
        ];
    }

    /**
     * Registers the routes for all available API controllers.
     */
    public function registerRoutes()
    {
        foreach ($this->controllers as $controller) {
            $controller->registerRoutes();
        }
    }
}
