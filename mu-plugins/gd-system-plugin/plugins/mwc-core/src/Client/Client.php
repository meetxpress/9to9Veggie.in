<?php

namespace GoDaddy\WordPress\MWC\Core\Client;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

/**
 * MWC Client class.
 *
 * @since x.y.z
 */
class Client
{
    /** @var string the app source, normally a URL */
    protected $appSource;

    /** @var string the identifier of the application */
    protected $appHandle;

    /**
     * MWC Client constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        if (! ManagedWooCommerceRepository::hasEcommercePlan()) {
            return;
        }

        $this->appHandle = 'mwcClient';
        $this->appSource = Configuration::get('mwc.client.index.url');

        $this->registerHooks();
    }

    /**
     * Registers the client's hook handlers.
     *
     * @since x.y.z
     *
     * @return Client
     * @throws Exception
     */
    protected function registerHooks() : Client
    {
        Register::action()
            ->setGroup('admin_enqueue_scripts')
            ->setHandler([$this, 'enqueueAssets'])
            ->execute();

        Register::action()
            ->setGroup('admin_print_styles')
            ->setHandler([$this, 'enqueueMessagesContainerStyles'])
            ->execute();

        Register::action()
            ->setGroup('all_admin_notices')
            ->setHandler([$this, 'renderMessagesContainer'])
            ->execute();

        return $this;
    }

    /**
     * Render the styles for the container div.
     *
     * @since x.y.z
     *
     * @return void
     * @throws Exception
     */
    public function enqueueMessagesContainerStyles()
    {
        Enqueue::style()
            ->setHandle("{$this->appHandle}-messages-styles")
            ->setSource(StringHelper::trailingSlash(Configuration::get('mwc.assets.styles')).'mwc-messages-container.css')
            ->execute();
    }

    /**
     * Render the styles for the container div.
     *
     * @since x.y.z
     *
     * @return void
     * @throws Exception
     */
    public function renderMessagesContainer()
    {
        ?>
        <div id="mwc-messages-container" class="mwc-messages-container"></div>
        <?php
    }

    /**
     * Enqueues/loads registered assets.
     *
     * @since x.y.z
     *
     * @throws Exception
     */
    public function enqueueAssets()
    {
        Enqueue::script()
               ->setHandle("{$this->appHandle}-runtime")
               ->setSource(Configuration::get('mwc.client.runtime.url'))
               ->setDeferred(true)
               ->execute();

        Enqueue::script()
               ->setHandle("{$this->appHandle}-vendors")
               ->setSource(Configuration::get('mwc.client.vendors.url'))
               ->setDeferred(true)
               ->execute();

        $this->enqueueApp();
    }

    /**
     * Enqueues the single page application script.
     *
     * @since x.y.z
     *
     * @throws Exception
     */
    protected function enqueueApp()
    {
        $script = Enqueue::script()
                         ->setHandle($this->appHandle)
                         ->setSource($this->appSource)
                         ->setDeferred(true);

        $inlineScriptVariables = $this->getInlineScriptVariables();

        if (!empty($inlineScriptVariables)) {
            $script->attachInlineScriptObject($this->appHandle)
                   ->attachInlineScriptVariables($inlineScriptVariables);
        }

        $script->execute();
    }

	/**
	 * Gets inline script variables.
	 *
	 * @since x.y.z
	 *
	 * @return array
	 */
	protected function getInlineScriptVariables() : array
	{
		return array_merge([
			'root'  => esc_url_raw(rest_url()),
			'nonce' => wp_create_nonce('wp_rest'),
		], $this->getPageContextVariables());
	}

	/**
	 * Gets inline script variables that describe the current page.
	 *
	 * @since x.y.z
	 *
	 * @return array
	 */
	protected function getPageContextVariables() : array
	{
		$currentScreen = WordPressRepository::getCurrentScreen();

		return $currentScreen ? $currentScreen->toArray() : [];
	}
}
