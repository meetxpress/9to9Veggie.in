<?php

namespace GoDaddy\WordPress\MWC\Core\Events\Producers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\ProducerContract;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Core\Events\PageViewEvent;
use WP_Screen;

class PageEventsProducer implements ProducerContract
{
    /**
     * Sets up the Page events producer.
     *
     * @throws Exception
     */
    public function setup()
    {
        Register::action()
            ->setGroup('current_screen')
            ->setHandler([$this, 'firePageViewEvent'])
            ->execute();
    }

    /**
     * Fires page view event.
     *
     * @internal
     *
     * @since x.y.z
     *
     * @param WP_Screen $currentWPScreen
     *
     * @throws Exception
     */
    public function firePageViewEvent($currentWPScreen)
    {
        if (! ArrayHelper::contains(['edit', 'post', 'woocommerce_page_wc-settings'], $currentWPScreen->base)) {
            return;
        }

        if ($currentScreen = WordPressRepository::getCurrentScreen()) {
            Events::broadcast(new PageViewEvent($currentScreen));
        }
    }
}
