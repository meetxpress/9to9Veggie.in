<?php

namespace GoDaddy\WordPress\MWC\Common\Pages;

use GoDaddy\WordPress\MWC\Common\Pages\Contracts\RenderableContract;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

/**
 * Abstract page class.
 *
 * Represents a base page for all pages to extend from
 *
 * @since 1.0.0
 */
abstract class AbstractPage implements RenderableContract
{
    /** @var string page screen identifier */
    protected $screenId;

    /** @var string page title */
    protected $pageTitle;

    /**
     * Abstract page constructor.
     *
     * @since 1.0.0
     *
     * @param string $screenId
     * @param string $pageTitle
     */
    public function __construct(string $screenId, string $pageTitle)
    {
        $this->screenId = $screenId;
        $this->pageTitle = $pageTitle;

        $this->registerAssets();
    }

    /**
     * Determines if the current page is the page we want to enqueue the registered assets.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    protected function shouldEnqueueAssets() : bool
    {
        return WordPressRepository::isCurrentPage('toplevel_page_'.strtolower($this->screenId));
    }

    /**
     * Renders the page HTML markup.
     *
     * @since 1.0.0
     */
    public function render()
    {
        //@NOTE implement render() method.
    }

    /**
     * Maybe enqueues the page necessary assets.
     *
     * @since 1.0.0
     */
    public function maybeEnqueueAssets()
    {
        if (! $this->shouldEnqueueAssets()) {
            return;
        }

        $this->enqueueAssets();
    }

    /**
     * Enqueues/loads registered the page assets.
     *
     * @since 1.0.0
     */
    protected function enqueueAssets()
    {
        //@NOTE implement assets loading for the page.
    }

    /**
     * Registers any page assets.
     *
     * @since 1.0.0
     */
    protected function registerAssets()
    {
        //@NOTE implement assets registration for the page
    }
}
