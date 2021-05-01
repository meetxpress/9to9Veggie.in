<?php

namespace GoDaddy\WordPress\MWC\Common\Pages;

use Exception;
use GoDaddy\WordPress\MWC\Common\Register\Register;

/**
 * Abstract WordPress Admin page class.
 *
 * Represents a base page for all WordPress admin pages to extend from.
 *
 * @since 1.0.0
 */
abstract class WordPressAdminPage extends AbstractPage
{
    /** @var string the minimum capability to have access to the related menu item */
    protected $capability;

    /** @var string the related menu title */
    protected $menuTitle;

    /** @var string the parent menu slug identifier */
    protected $parentMenuSlug;

    /**
     * WordPress admin page constructor.
     *
     * @since 1.0.0
     *
     * @param string $screenId
     * @param string $pageTitle
     */
    public function __construct(string $screenId, string $pageTitle)
    {
        parent::__construct($screenId, $pageTitle);

        $this->registerMenuItem();
    }

    /**
     * Adds the menu page.
     *
     * @since 1.0.0
     *
     * @internal
     *
     * @see https://developer.wordpress.org/reference/functions/add_submenu_page/
     *
     * @return self
     */
    public function addMenuItem() : self
    {
        if (empty($this->parentMenuSlug)) {
            // TODO: log an error using a wrapper for WC_Logger {WV 2021-02-15}
            // throw new Exception('The page parent menu slug property should be defined.');
        }

        add_submenu_page(
            $this->parentMenuSlug,
            $this->pageTitle,
            $this->menuTitle ?? $this->pageTitle,
            $this->capability,
            $this->screenId,
            [$this, 'render']
        );

        return $this;
    }

    /**
     * Registers the menu page.
     *
     * @since 1.0.0
     *
     * @return self
     */
    protected function registerMenuItem() : self
    {
        try {
            if ($this->shouldAddMenuItem()) {
                Register::action()
                    ->setGroup('admin_menu')
                    ->setHandler([$this, 'addMenuItem'])
                    ->execute();
            }
        } catch (Exception $ex) {
            // TODO: log an error using a wrapper for WC_Logger {WV 2021-02-15}
            // throw new Exception('Cannot register the menu item: '.$ex->getMessage());
        }

        return $this;
    }

    /**
     * Checks if the menu item for this page should be added/registered or not.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    protected function shouldAddMenuItem() : bool
    {
        return true;
    }

    /**
     * Registers the page assets.
     *
     * @since 1.0.0
     *
     * @return self
     */
    protected function registerAssets() : self
    {
        try {
            Register::action()
                ->setGroup('admin_enqueue_scripts')
                ->setHandler([$this, 'maybeEnqueueAssets'])
                ->execute();
        } catch (Exception $ex) {
            // TODO: log an error using a wrapper for WC_Logger {WV 2021-02-15}
            // throw new Exception('Cannot register assets: '.$ex->getMessage());
        }

        return $this;
    }
}
