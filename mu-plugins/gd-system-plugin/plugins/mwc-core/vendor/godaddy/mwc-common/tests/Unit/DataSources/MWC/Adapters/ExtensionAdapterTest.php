<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\DataSources\MWC\Adapters;

use ErrorException;
use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\DataSources\MWC\Adapters\ExtensionAdapter;
use GoDaddy\WordPress\MWC\Common\DataSources\SkyVerge\Adapters\SkyVergeExtensionAdapter;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\DataSources\MWC\Adapters\ExtensionAdapter
 */
final class ExtensionAdapterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that can convert from source.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\MWC\Adapters\ExtensionAdapter::convertFromSource()
     * @throws Exception
     */
    public function testCanConvertFromSource()
    {
        $adapter = new ExtensionAdapter($this->getSourceData());

        $this->assertSame([
            'id'                        => '1001',
            'slug'                      => 'test-plugin',
            'name'                      => 'Test Plugin',
            'shortDescription'          => 'Test Plugin description',
            'type'                      => PluginExtension::TYPE,
            'version'                   => '1.2.3',
            'lastUpdated'               => 1610151181,
            'minimumPhpVersion'         => '7.0',
            'minimumWordPressVersion'   => '5.2',
            'minimumWooCommerceVersion' => '3.5',
            'packageUrl'                => 'https://example.org/package',
            'homepageUrl'               => 'https://example.org/homepage',
            'documentationUrl'          => 'https://example.org/documentation',
            'imageUrls'                 => [
                '1x' => 'url1',
                '2x' => 'url2',
            ],
            'brand'                     => 'test brand',
            'basename'                  => 'test-plugin/test-plugin.php',
        ], $adapter->convertFromSource());
    }

    /**
     * Gets source data used for tests.
     */
    private function getSourceData(): array
    {
        return [
            'extensionId'      => '1001',
            'slug'             => 'test-plugin',
            'label'            => 'Test Plugin',
            'shortDescription' => 'Test Plugin description',
            'type'             => 'PLUGIN',
            'category'         => null,
            'imageUrls'        => [
                '1x' => 'url1',
                '2x' => 'url2',
            ],
            'version'          => [
                'version'                   => '1.2.3',
                'minimumPhpVersion'         => '7.0',
                'minimumWordPressVersion'   => '5.2',
                'minimumWooCommerceVersion' => '3.5',
                'releasedAt'                => '2021-01-09T00:13:01.000000Z',
                'links'                     => [
                    'package' => [
                        'href' => 'https://example.org/package',
                    ],
                ],
            ],
            'links'             => [
                'homepage'      => [
                    'href' => 'https://example.org/homepage',
                ],
                'documentation' => [
                    'href' => 'https://example.org/documentation',
                ],
            ],
            'brand'             => 'test brand',
        ];
    }

    /**
     * Tests that it can convert to source.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\MWC\Adapters\ExtensionAdapter::convertToSource()
     */
    public function testCanConvertToSource()
    {
        $adapter = new ExtensionAdapter($this->getSourceData());

        $this->assertSame($this->getSourceData(), $adapter->convertToSource());
    }

    /**
     * Tests that it can get the type.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\MWC\Adapters\ExtensionAdapter::getType()
     */
    public function testCanGetType()
    {
        $adapter = new ExtensionAdapter($this->getSourceData());

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
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\MWC\Adapters\ExtensionAdapter::getImageUrls()
     */
    public function testCanGetImageUrls(array $data, array $expected)
    {
        $adapter = new ExtensionAdapter($data);

        $this->assertSame($expected, $adapter->getImageUrls());
    }

    /** @see testCanGetImageUrls */
    public function providerCanGetImageUrls() : array
    {
        return [
            'valid data' => [
                [
                    'imageUrls' => [
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
                    'no URLs' => [],
                ],
                [],
            ],
            'not an array' => [
                [
                    'imageUrls' => 'url',
                ],
                [
                    'url',
                ],
            ],
            'invalid URLs' => [
                [
                    'imageUrls' => [
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

    /**
     * Tests that the brand handling is properly done inside getExtensionData.
     *
     * @dataProvider providerCanHandleBrand
     *
     * @param string $brand brand returned from the API (can be null)
     * @param string $expected expected brand value after handling
     *
     * @covers \GoDaddy\WordPress\MWC\Common\DataSources\MWC\Adapters\ExtensionAdapter::getExtensionData()
     * @throws Exception
     */
    public function testCanHandleBrand($brand, string $expected)
    {
        $sourceData = $this->getSourceData();
        $sourceData['brand'] = $brand;

        $adapter = new ExtensionAdapter($sourceData);

        $convertedData = $adapter->convertFromSource();

        $this->assertEquals($expected, $convertedData['brand']);
    }

    /** @see testCanHandleBrand */
    public function providerCanHandleBrand() : array
    {
        return [
            'null brand'        => [null, 'godaddy'],
            'empty brand'       => ['', 'godaddy'],
            'brand defined'     => ['brand name', 'brand name'],
            'capitalized brand' => ['Brand Name', 'brand name'],
        ];
    }

    /**
     * Tests that the old SkyVergeExtensionAdapter class is still instantiable after being renamed.
     */
    public function testCanInstantiateSkyVergeExtensionAdapter()
    {
        WP_Mock::userFunction('get_transient', [
            'return' => ['wordpress.absolute_path' => 'foo/bar'],
        ]);

        // ensure deprecated notices are thrown
        Configuration::set('mwc.debug', true);

        // TODO: find out why we need to handle deprecations differently {WV 2021-03-29}
        if (version_compare(PHP_VERSION, '8.0', '>')) {
            $this->expectDeprecation();
        } else {
            $this->expectException(ErrorException::class);
            $this->expectErrorMessageMatches('/is deprecated since version/');
        }

        $this->assertNotNull(new SkyVergeExtensionAdapter($this->getSourceData()));
    }
}
