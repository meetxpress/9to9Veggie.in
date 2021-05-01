<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\DataSources\WooCommerce\Adapters;

use Exception;
use GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\WooCommerceExtensionAdapter;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\WooCommerceExtensionAdapter
 */
final class WooCommerceExtensionAdapterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that it can convert from source.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\WooCommerceExtensionAdapter::convertFromSource()
     * @throws Exception
     */
    public function testCanConvertFromSource()
    {
        $adapter = new WooCommerceExtensionAdapter($this->getSourceData());

        $this->assertSame([
            'slug'                      => 'test-plugin',
            'name'                      => 'Test Plugin',
            'shortDescription'          => 'Test Plugin description',
            'type'                      => PluginExtension::TYPE,
            'version'                   => '1.2.3',
            'lastUpdated'               => 1602806400,
            'packageUrl'                => 'https://example.org/package',
            'homepageUrl'               => 'https://example.org/homepage',
            'documentationUrl'          => 'https://example.org/documentation',
            'imageUrls'                 => [
                '1x' => 'https://example.org/icons-128x128.png',
                '2x' => 'https://example.org/icons-256x256.png',
            ],
            'basename'                  => 'test-plugin/test-plugin.php',
        ], $adapter->convertFromSource());
    }

    /**
     * Gets WooCommerce source data used for tests.
     */
    private function getSourceData(): array
    {
        return [
            'download_link'         => 'https://example.org/package',
            'homepage'              => 'https://example.org/homepage',
            'icons'                 => [
                '1x' => 'https://example.org/icons-128x128.png',
                '2x' => 'https://example.org/icons-256x256.png',
            ],
            'last_updated'          => '2020-10-16',
            'name'                  => 'Test Plugin',
            'short_description'     => 'Test Plugin description',
            'slug'                  => 'test-plugin',
            'support_documentation' => 'https://example.org/documentation',
            'type'                  => PluginExtension::TYPE,
            'category'              => null,
            'version'               => '1.2.3',
        ];
    }

    /**
     * Tests that it can convert from source when using a non-standard plugin basename.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\WooCommerceExtensionAdapter::getPluginBasename()
     */
    public function testConvertFromSourceReturnsNonStandardBasename()
    {
        $adapter = new WooCommerceExtensionAdapter([
            'slug' => 'woocommerce-product-enquiry-form',
            'type' => PluginExtension::TYPE,
        ]);

        $this->assertSame('woocommerce-product-enquiry-form/product-enquiry-form.php', ArrayHelper::get($adapter->convertFromSource(), 'basename'));
    }

    /**
     * Tests that it can convert to source.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\WooCommerceExtensionAdapter::convertToSource()
     */
    public function testCanConvertToSource()
    {
        $adapter = new WooCommerceExtensionAdapter($this->getSourceData());

        $this->assertSame($this->getSourceData(), $adapter->convertToSource());
    }

    /**
     * Tests that it can get the type.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\WooCommerceExtensionAdapter::getType()
     */
    public function testCanGetType()
    {
        $adapter = new WooCommerceExtensionAdapter($this->getSourceData());

        $this->assertSame(PluginExtension::TYPE, $adapter->getType());
    }

    /**
     * Tests that it can get valid image URLs.
     *
     * @dataProvider providerCanGetImageUrls
     *
     * @param array $data input data
     * @param array $expected expected return
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\WooCommerceExtensionAdapter::getImageUrls()
     */
    public function testCanGetImageUrls(array $data, array $expected)
    {
        $adapter = new WooCommerceExtensionAdapter($data);

        $this->assertSame($expected, $adapter->getImageUrls());
    }

    /** @see testCanGetImageUrls */
    public function providerCanGetImageUrls() : array
    {
        return [
            'valid data' => [
                [
                    'icons' => [
                        '1x' => 'url1',
                        '2x' => 'url2',
                    ],
                ],
                [
                    '1x' => 'url1',
                    '2x' => 'url2',
                ],
            ],
            'missing data' => [
                [
                    'no icons' => [],
                ],
                [],
            ],
            'not an array' => [
                [
                    'icons' => 'url',
                ],
                [
                    'url',
                ],
            ],
            'invalid URLs' => [
                [
                    'icons' => [
                        'empty string' => '',
                        'not a string' => 1234,
                        'valid'        => 'url1',
                    ],
                ],
                [
                    'valid' => 'url1',
                ],
            ],
        ];
    }
}
