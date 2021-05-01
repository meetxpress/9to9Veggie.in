<?php

namespace GoDaddy\WordPress\MWC\Core\Events;

use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventBridgeEventContract;
use GoDaddy\WordPress\MWC\Common\Pages\Context\Screen;
use GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;

/**
 * Page view event class.
 *
 * @since x.y.z
 */
class PageViewEvent implements EventBridgeEventContract
{
    use IsEventBridgeEventTrait;

    /** @var Screen The WordPress screen object */
    protected $screen;

    /**
     * PageViewEvent constructor.
     *
     * @param Screen $screen
     */
    public function __construct(Screen $screen)
    {
        $this->resource = 'page';
        $this->action   = 'view';
        $this->screen   = $screen;
    }

    /**
     * Gets the data for the event.
     *
     * @return array
     */
    public function getData() : array
    {
        return $this->screen->toArray();
    }
}
