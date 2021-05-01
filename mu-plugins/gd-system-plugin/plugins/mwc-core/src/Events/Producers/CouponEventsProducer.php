<?php

namespace GoDaddy\WordPress\MWC\Core\Events\Producers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\ProducerContract;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\CouponsRepository;
use GoDaddy\WordPress\MWC\Core\Events\CouponCreatedEvent;
use GoDaddy\WordPress\MWC\Core\Events\CouponUpdatedEvent;
use GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag;
use WP_Post;

class CouponEventsProducer implements ProducerContract
{
    /**
     * Sets up the Coupon events producer.
     *
     * @throws Exception
     */
    public function setup()
    {
        Register::action()
            ->setGroup('wp_insert_post')
            ->setHandler([$this, 'maybeFlagNewCoupon'])
            ->setArgumentsCount(3)
            ->execute();

        Register::action()
            ->setGroup('woocommerce_process_shop_coupon_meta')
            ->setHandler([$this, 'maybeFireCouponEvents'])
            ->execute();
    }

    /**
     * Turns the new coupon flag on if the post created was a coupon.
     *
     * @param $postId
     * @param WP_Post $post
     */
    public function maybeFlagNewCoupon($postId, $post, $isUpdate)
    {
        if (! $isUpdate && $post->post_type === 'shop_coupon') {
            $this->getNewCouponFlag((int) $postId)->turnOn();
        }
    }

    /**
     * Fires coupon created/updated events.
     *
     * @param int $postId
     * @throws Exception
     */
    public function maybeFireCouponEvents($postId)
    {
        if (! ($coupon = CouponsRepository::get($postId))) {
            return;
        }

        $newCouponFlag = $this->getNewCouponFlag($coupon->get_id());

        if ($newCouponFlag->isOn()) {
            Events::broadcast((new CouponCreatedEvent())->setWooCommerceCoupon($coupon));

            $newCouponFlag->turnOff();
        } else {
            Events::broadcast((new CouponUpdatedEvent())->setWooCommerceCoupon($coupon));
        }
    }

    /**
     * Gets the new coupon flag instance for the given coupon id.
     *
     * @param int $couponId
     * @return NewWooCommerceObjectFlag
     */
    protected function getNewCouponFlag(int $couponId) : NewWooCommerceObjectFlag
    {
        return new NewWooCommerceObjectFlag($couponId);
    }
}
