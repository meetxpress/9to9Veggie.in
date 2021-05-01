<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Traits;

use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait;
use Mockery;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait
 */
final class HasWooCommerceMetaTraitTest extends WPTestCase
{
    /**
     * Tests that can get a WooCommerce meta default value if the meta doesn't exist using an object as the source.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait::loadWooCommerceMeta()
     * @dataProvider providerGetMetaData
     *
     * @param string $metaKey
     * @param mixed $metaValue
     * @param mixed $defaultValue
     */
    public function testCanLoadWooCommerceMetaFromObjectAndReturnDefaultValue(string $metaKey, $metaValue, $defaultValue)
    {
        $object = Mockery::mock('WC_Data');
        $object->shouldReceive('meta_exists')
               ->once()
               ->withArgs([$metaKey])
               ->andReturn(false);

        $trait = $this->getMockInstance($object, $metaKey);

        $this->assertSame($defaultValue, $trait->loadWooCommerceMeta($defaultValue));
    }

    /**
     * Tests that can get a WooCommerce meta default value if the meta doesn't exist using an ID as the source.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait::loadWooCommerceMeta()
     * @dataProvider providerGetMetaData
     *
     * @param string $metaKey
     * @param mixed $metaValue
     * @param mixed $defaultValue
     */
    public function testCanLoadWooCommerceMetaFromIdAndReturnDefaultValue(string $metaKey, $metaValue, $defaultValue)
    {
        $trait = $this->getMockInstance(123, $metaKey, $metaValue);

        WP_Mock::userFunction('metadata_exists')
               ->withArgs(['post', 123, $metaKey])
               ->once()
               ->andReturn(false);

        $this->assertSame($defaultValue, $trait->loadWooCommerceMeta($defaultValue));
    }

    /**
     * Tests that can get the WooCommerce meta value using an object as the source.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait::loadWooCommerceMeta()
     * @dataProvider providerGetMetaData
     *
     * @param string $metaKey
     * @param mixed $metaValue
     * @param mixed $defaultValue
     */
    public function testCanLoadWooCommerceMetaFromObjectAndReturnActualValue(string $metaKey, $metaValue, $defaultValue)
    {
        $object = Mockery::mock('WC_Data');
        $object->shouldReceive('meta_exists')
               ->once()
               ->withArgs([$metaKey])
               ->andReturn(true);
        $object->shouldReceive('get_meta')
               ->once()
               ->withArgs([$metaKey, true])
               ->andReturn($metaValue);

        $trait = $this->getMockInstance($object, $metaKey, $metaValue);

        $this->assertSame($metaValue, $trait->loadWooCommerceMeta($defaultValue));
    }


    /**
     * Tests that can get the WooCommerce meta value using an object as the source.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait::loadWooCommerceMeta()
     * @dataProvider providerGetMetaData
     *
     * @param string $metaKey
     * @param mixed $metaValue
     * @param mixed $defaultValue
     */
    public function testCanLoadWooCommerceMetaFromIdAndReturnActualValue(string $metaKey, $metaValue, $defaultValue)
    {
        $trait = $this->getMockInstance(123, $metaKey, $metaValue);

        WP_Mock::userFunction('metadata_exists')
               ->withArgs(['post', 123, $metaKey])
               ->once()
               ->andReturn(true);

        WP_Mock::userFunction('get_post_meta')
               ->withArgs([123, $metaKey, true])
               ->once()
               ->andReturn($metaValue);

        $this->assertSame($metaValue, $trait->loadWooCommerceMeta($defaultValue));
    }

    /**
     * Tests that can get the WooCommerce meta value set in property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait::getWooCommerceMeta()
     * @dataProvider providerGetMetaData
     *
     * @param string $metaKey
     * @param $metaValue
     * @param $defaultValue
     */
    public function testCanGetWooCommerceMeta(string $metaKey, $metaValue, $defaultValue)
    {
        $trait = $this->getMockInstance(123, $metaKey, $metaValue);

        $this->assertSame($metaValue, $trait->getWooCommerceMeta());
    }

    /**
     * Tests that can set the WooCommerce meta value in property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait::setWooCommerceMeta()
     * @dataProvider providerGetMetaData
     *
     * @param string $metaKey
     * @param $metaValue
     * @param $defaultValue
     */
    public function testCanSetWooCommerceMeta(string $metaKey, $metaValue, $defaultValue)
    {
        $trait = $this->getMockInstance(123, $metaKey, null);

        $this->assertSame($trait, $trait->setWooCommerceMeta($metaValue));
        $this->assertSame($metaValue, $trait->getWooCommerceMeta());
    }

    /**
     * Tests that can save the WooCommerce meta and return self, using an object.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait::saveWooCommerceMeta()
     * @dataProvider providerGetMetaData
     *
     * @param string $metaKey
     * @param mixed $metaValue
     * @param mixed $defaultValue
     */
    public function testCanSaveWooCommerceMetaUsingAnObject(string $metaKey, $metaValue, $defaultValue)
    {
        $object = Mockery::mock('WC_Data');
        $object->shouldReceive('update_meta_data')
               ->once()
               ->withArgs([$metaKey, $metaValue]);
        $object->shouldReceive('save_meta_data')
               ->once();

        $trait = $this->getMockInstance($object, $metaKey, $metaValue);

        $this->assertSame($trait, $trait->saveWooCommerceMeta());
    }

    /**
     * Tests that can delete the WooCommerce meta and return self, using an object.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait::deleteWooCommerceMeta()
     * @dataProvider providerGetMetaData
     *
     * @param string $metaKey
     * @param mixed $metaValue
     * @param mixed $defaultValue
     */
    public function testCanDeleteWooCommerceMetaUsingAnObject(string $metaKey, $metaValue, $defaultValue)
    {
        $object = Mockery::mock('WC_Data');
        $object->shouldReceive('delete_meta_data')
               ->once();
        $object->shouldReceive('save_meta_data')
               ->once();

        $trait = $this->getMockInstance($object, $metaKey, $metaValue);

        $this->assertSame($trait, $trait->deleteWooCommerceMeta());
    }

    /**
     * Tests that can save the WooCommerce meta and return self, using an ID.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait::saveWooCommerceMeta()
     * @dataProvider providerGetMetaData
     *
     * @param string $metaKey
     * @param mixed $metaValue
     * @param mixed $defaultValue
     */
    public function testCanSaveWooCommerceMetaUsingAnId(string $metaKey, $metaValue, $defaultValue)
    {
        $trait = $this->getMockInstance(123, $metaKey, $metaValue);

        WP_Mock::userFunction('update_post_meta')
               ->withArgs([123, $metaKey, $metaValue])
               ->once();

        $this->assertSame($trait, $trait->saveWooCommerceMeta());
    }

    /**
     * Tests that can delete the WooCommerce meta and return self, using an ID.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Traits\HasWooCommerceMetaTrait::deleteWooCommerceMeta()
     * @dataProvider providerGetMetaData
     *
     * @param string $metaKey
     * @param mixed $metaValue
     * @param mixed $defaultValue
     */
    public function testCanDeleteWooCommerceMetaUsingAnId(string $metaKey, $metaValue, $defaultValue)
    {
        $trait = $this->getMockInstance(123, $metaKey, $metaValue);

        WP_Mock::userFunction('delete_post_meta')
               ->withArgs([123, $metaKey])
               ->once();

        $this->assertSame($trait, $trait->deleteWooCommerceMeta());
    }

    /**
     * Gets a mock instance implementing the trait.
     *
     * @param \WC_Data|int $objectOrId
     * @param string $metaKey
     * @param mixed|null $metaValue
     * @return object|HasWooCommerceMetaTrait
     */
    private function getMockInstance($objectOrId, string $metaKey, $metaValue = null)
    {
        return new class($objectOrId, $metaKey, $metaValue) {
            // trick to turn protected method accessible for sake of testing
            use HasWooCommerceMetaTrait {
                loadWooCommerceMeta as protected traitLoadWooCommerceMeta;
                getWooCommerceMeta as protected traitGetWooCommerceMeta;
                setWooCommerceMeta as protected traitSetWooCommerceMeta;
                saveWooCommerceMeta as protected traitSaveWooCommerceMeta;
                deleteWooCommerceMeta as protected traitDeleteWooCommerceMeta;
            }

            public function __construct($objectOrId, $metaKey, $value)
            {
                $this->objectOrObjectId = $objectOrId;
                $this->metaKey = $metaKey;
                $this->metaValue = $value;
            }

            public function loadWooCommerceMeta($defaultValue = null)
            {
                return $this->traitLoadWooCommerceMeta($defaultValue);
            }

            public function getWooCommerceMeta()
            {
                return $this->traitGetWooCommerceMeta();
            }

            public function setWooCommerceMeta($value = null)
            {
                return $this->traitSetWooCommerceMeta($value);
            }

            public function saveWooCommerceMeta()
            {
                return $this->traitSaveWooCommerceMeta();
            }

            public function deleteWooCommerceMeta()
            {
                return $this->traitDeleteWooCommerceMeta();
            }
        };
    }

    /** @see HasWooCommerceMetaTraitTest tests */
    public function providerGetMetaData() : array
    {
        return [
            ['meta_key', 'value', 'default'],
            ['meta_key', ['foo'], ['bar']],
            ['meta_key', 5, 10],
            ['meta_key', 2.71, 3.14],
            ['meta_key', true, false],
            ['meta_key', false, true]
        ];
    }
}
