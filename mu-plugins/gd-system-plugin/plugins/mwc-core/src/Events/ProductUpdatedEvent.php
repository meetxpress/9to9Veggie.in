<?php

namespace GoDaddy\WordPress\MWC\Core\Events;

/**
 * Product updated event class.
 *
 * @since x.y.z
 */
class ProductUpdatedEvent extends AbstractProductEvent
{
    /**
     * ProductUpdatedEvent constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->action = 'update';
    }
}
