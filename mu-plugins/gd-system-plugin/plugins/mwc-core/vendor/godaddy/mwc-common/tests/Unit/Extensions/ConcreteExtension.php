<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Extensions;

use GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension;

/**
 * Subclass of AbstractExtension used as the test subject in AbstractExtensionTest.
 *
 * @see AbstractExtension
 */
final class ConcreteExtension extends AbstractExtension
{
    /** @var string type */
    const TYPE = 'concrete';

    /**
     * ConcreteExtension constructor.
     */
    public function __construct()
    {
        $this->setType(self::TYPE);
    }
}
