<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Traits;

use GoDaddy\WordPress\MWC\Common\Traits\CanBulkAssignPropertiesTrait;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Traits\CanBulkAssignPropertiesTrait
 */
final class CanBulkAssignPropertiesTraitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that it can set properties.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\CanBulkAssignPropertiesTrait::setProperties()
     */
    public function testCanSetProperties()
    {
        $object = $this->getTestSubject();

        $this->assertNull($object->getPrivatePropertyWithSetter());
        $this->assertNull($object->publicPropertyWithSetter);
        $this->assertNull($object->publicPropertyWithoutSetter);

        $object->setProperties([
            'privatePropertyWithSetter'    => 'value',
            'publicPropertyWithSetter'    => 'value',
            'publicPropertyWithoutSetter' => 'value',
        ]);

        $this->assertSame('value', $object->getPrivatePropertyWithSetter());
        $this->assertSame('value', $object->publicPropertyWithSetter);
        $this->assertNull($object->publicPropertyWithoutSetter);
    }

    private function getTestSubject()
    {
        return new class {
            use CanBulkAssignPropertiesTrait;

            private $privatePropertyWithSetter;

            public $publicPropertyWithSetter;

            public $publicPropertyWithoutSetter;

            public function getPrivatePropertyWithSetter()
            {
                return $this->privatePropertyWithSetter;
            }

            public function setPrivatePropertyWithSetter($value)
            {
                $this->privatePropertyWithSetter = $value;
            }

            public function setPublicPropertyWithSetter($value)
            {
                $this->publicPropertyWithSetter = $value;
            }
        };
    }
}
