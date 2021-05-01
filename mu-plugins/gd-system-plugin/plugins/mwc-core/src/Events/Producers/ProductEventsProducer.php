<?php

namespace GoDaddy\WordPress\MWC\Core\Events\Producers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\ProducerContract;
use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository;
use GoDaddy\WordPress\MWC\Core\Events\ProductCreatedEvent;
use GoDaddy\WordPress\MWC\Core\Events\ProductUpdatedEvent;
use GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag;
use WP_Post;

class ProductEventsProducer implements ProducerContract
{
    /**
     * Sets up the Product events producer.
     *
     * @throws Exception
     */
    public function setup()
    {
        Register::action()
            ->setGroup('wp_insert_post')
            ->setHandler([$this, 'maybeFlagNewProduct'])
            ->setArgumentsCount(3)
            ->execute();

        Register::action()
            ->setGroup('woocommerce_process_product_meta')
            ->setHandler([$this, 'maybeFireProductEvents'])
            ->execute();
    }

    /**
     * Turns the new product flag on if the post created was a product.
     *
     * @param $postId
     * @param WP_Post $post
     */
    public function maybeFlagNewProduct($postId, $post, $isUpdate)
	{
        if (! $isUpdate && $post->post_type === 'product') {
            $this->getNewProductFlag((int) $postId)->turnOn();
        }
    }

    /**
     * Fires product created/updated events.
     *
     * @param int $postId
     *
     * @throws Exception
     */
    public function maybeFireProductEvents($postId)
    {
        if (! ($product = ProductsRepository::get((int) $postId))) {
            return;
        }

        $newProductFlag = $this->getNewProductFlag($product->get_id());

        if ($newProductFlag->isOn()) {
            Events::broadcast((new ProductCreatedEvent())->setWooCommerceProduct($product));

            $newProductFlag->turnOff();
        } else {
            Events::broadcast((new ProductUpdatedEvent())->setWooCommerceProduct($product));
        }
    }

    /**
     * Gets the new product flag instance for the given product id.
     *
     * @param int $productId
     * @return NewWooCommerceObjectFlag
     */
    protected function getNewProductFlag(int $productId) : NewWooCommerceObjectFlag
    {
        return new NewWooCommerceObjectFlag($productId);
    }
}
