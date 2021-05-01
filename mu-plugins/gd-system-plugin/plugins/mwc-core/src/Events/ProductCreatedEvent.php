<?php

namespace GoDaddy\WordPress\MWC\Core\Events;

/**
 * Product created event class.
 *
 * @since x.y.z
 */
class ProductCreatedEvent extends AbstractProductEvent
{
    /**
     * ProductCreatedEvent constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->action = 'create';
    }
}
