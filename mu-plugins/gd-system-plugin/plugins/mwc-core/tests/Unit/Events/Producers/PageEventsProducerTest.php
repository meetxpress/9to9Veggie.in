<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\Events\Producers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Core\Events\Producers\PageEventsProducer;
use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use Mockery;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\PageEventsProducer
 */
final class PageEventsProducerTest extends WPTestCase
{
    /**
     * Tests that the producer sets up properly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\PageEventsProducer::setup()
     * @throws Exception
     */
    public function testCanSetup()
    {
        $producer = new PageEventsProducer();

        WP_Mock::expectActionAdded('current_screen', [$producer, 'firePageViewEvent']);
        $producer->setup();

        $this->assertConditionsMet();
    }

    /**
     * Tests that the producer fires page view event correctly.
     *
     * @covers \GoDaddy\WordPress\MWC\Core\Events\Producers\PageEventsProducer::firePageViewEvent()
     * @dataProvider providerCanFirePageViewEvent
     *
     * @param array $screenArgs
     * @param bool $shouldFire
     *
     * @throws Exception
     */
    public function testCanFirePageViewEvent(array $screenArgs, bool $shouldFire)
    {
        $currentWPScreen = Mockery::mock('WP_Screen');
        $currentWPScreen->base = $screenArgs['base'];
        $currentWPScreen->post_type = $screenArgs['postType'];
        $currentWPScreen->action = $screenArgs['action'];

        $eventsClass = Mockery::mock('alias:'.Events::class);
        if ($shouldFire) {
            WP_Mock::userFunction('get_post_status')->withAnyArgs();
            WP_Mock::userFunction('get_current_screen')->once()->andReturn($currentWPScreen);

            $eventsClass->shouldReceive('broadcast');
        } else {
            $eventsClass->shouldNotReceive('broadcast');
        }

        (new PageEventsProducer())->firePageViewEvent($currentWPScreen);

        $this->assertConditionsMet();
    }

    /**
     * @see testCanFirePageViewEvent
     *
     * @return array
     */
    public function providerCanFirePageViewEvent() : array
    {
        return [
            'List products'             => [['base' => 'edit', 'postType' => 'product', 'action' => 'add'], true],
            'Edit coupon'               => [['base' => 'post', 'postType' => 'coupon', 'action' => 'edit'], true],
            'Add Post'                  => [['base' => 'post', 'postType' => 'post', 'action' => 'add'], true],
            'WooCommerce Settings page' => [['base' => 'woocommerce_page_wc-settings', 'postType' => '', 'action' => ''], true],
            'Other page'                => [['base' => 'page', 'postType' => '', 'action' => ''], false],
        ];
    }
}
