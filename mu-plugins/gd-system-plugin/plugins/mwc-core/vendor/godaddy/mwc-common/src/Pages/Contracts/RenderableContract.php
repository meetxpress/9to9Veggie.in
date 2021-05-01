<?php

namespace GoDaddy\WordPress\MWC\Common\Pages\Contracts;

/**
 * Renderable page contract/interface.
 *
 * @since 1.0.0
 */
interface RenderableContract
{
    /**
     * Renders page markup.
     *
     * @since 1.0.0
     */
    public function render();
}
