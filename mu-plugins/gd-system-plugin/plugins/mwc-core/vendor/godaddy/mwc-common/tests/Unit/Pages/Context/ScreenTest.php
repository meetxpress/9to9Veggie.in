<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Pages\Context;

use GoDaddy\WordPress\MWC\Common\Pages\Context\Screen;
use PHPUnit\Framework\TestCase;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen
 */
final class ScreenTest extends TestCase
{
    /**
     * Tests screen constructor.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::__construct()
     *
     * @dataProvider providerCanSetScreenPropertiesValues
     *
     * @param array $data
     */
    public function testCanConstructScreenInstance(array $data)
    {
        $screen = new Screen($data);

        $this->assertSame($data['pageId'], $screen->getPageId());
    }

    /**
     * Tests screen getters.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::getPageId()
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::getPageContexts()
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::getObjectId()
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::getObjectStatus()
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::getObjectType()
     *
     * @dataProvider providerCanSetScreenPropertiesValues
     *
     * @param array $data
     */
    public function testCanGetScreenPropertiesValues(array $data)
    {
        $screen = new Screen($data);

        $this->assertSame($data['pageId'], $screen->getPageId());
        $this->assertSame($data['pageContexts'], $screen->getPageContexts());
        $this->assertSame($data['objectId'], $screen->getObjectId());
        $this->assertSame($data['objectType'], $screen->getObjectType());
        $this->assertSame($data['objectStatus'], $screen->getObjectStatus());
    }

    /**
     * Tests screen setters.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::setObjectId()
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::setPageContexts()
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::setObjectId()
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::setObjectStatus()
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::setObjectType()
     *
     * @dataProvider providerCanSetScreenPropertiesValues
     *
     * @param array $data
     */
    public function testCanSetScreenPropertiesValues(array $data)
    {
        $screen = new Screen([]);

        foreach ($data as $name => $value) {
            $setterMethodName = 'set'.ucfirst($name);
            $getterMethodName = 'get'.ucfirst($name);

            $this->assertInstanceOf(Screen::class, $screen->$setterMethodName($value));
            $this->assertSame($value, $screen->$getterMethodName());
        }
    }

    /**
     * Tests screen toArray.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\Context\Screen::toArray()
     *
     * @dataProvider providerCanConvertScreenDataToArrayFormat
     *
     * @param array $data
     * @param array $toArray
     */
    public function testCanConvertScreenDataToArrayFormat(array $data, array $toArray)
    {
        $screen = new Screen([]);

        foreach ($data as $name => $value) {
            $setterMethodName = 'set'.ucfirst($name);
            $screen->$setterMethodName($value);
        }

        $screenToArray = $screen->toArray();

        $this->assertIsArray($screenToArray);
        $this->assertSame($toArray, $screenToArray);
    }

    /** @see ScreenTest tests */
    public function providerCanConvertScreenDataToArrayFormat() : array
    {
        return [
            'All data' => [
                [
                    'pageId'       => 'some_product',
                    'pageContexts' => ['woocommerce'],
                    'objectId'     => '123',
                    'objectType'   => 'product',
                    'objectStatus' => 'publish',
                ],
                [
                    'page'   => [
                        'id'       => 'some_product',
                        'contexts' => ['woocommerce'],
                    ],
                    'object' => [
                        'id'     => '123',
                        'type'   => 'product',
                        'status' => 'publish',
                    ],
                ]
            ],
            'Some data' => [
                [
                    'pageId'       => 'some_page',
                    'objectId'     => '456',
                    'objectStatus' => 'draft'
                ],
                [
                    'page'   => [
                        'id' => 'some_page',
                    ],
                    'object' => [
                        'id'     => '456',
                        'status' => 'draft',
                    ],
                ]
            ],
            'Nothing' => [
                [
                    'pageId'       => '',
                    'objectId'     => '',
                    'objectStatus' => ''
                ],
                []
            ]
        ];
    }

    /** @see ScreenTest tests */
    public function providerCanSetScreenPropertiesValues() : array
    {
        return [
            [
                [
                    'pageId'       => 'some_product',
                    'pageContexts' => ['woocommerce'],
                    'objectId'     => '123',
                    'objectType'   => 'product',
                    'objectStatus' => 'publish',
                ]
            ],
            [
                [
                    'pageId'       => 'some_page',
                    'pageContexts' => ['wordpress'],
                    'objectId'     => '456',
                    'objectType'   => 'page',
                    'objectStatus' => 'draft',
                ]
            ]
        ];
    }
}
