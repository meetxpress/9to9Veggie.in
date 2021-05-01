<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Extensions;

use GoDaddy\WordPress\MWC\Common\Exceptions\ExtensionDownloadFailedException;
use GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use Mockery;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension
 */
final class AbstractExtensionTest extends WPTestCase
{
    /**
     * Tests that can return the ID property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getId()
     */
    public function testCanGetId()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getId());

        $mock->setId('test-id');

        self::assertEquals('test-id', $mock->getId());
    }

    /**
     * Tests that can return the slug property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getSlug()
     */
    public function testCanGetSlug()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getSlug());

        $mock->setSlug('test-slug');

        self::assertEquals('test-slug', $mock->getSlug());
    }

    /**
     * Tests that can return the name property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getName()
     */
    public function testCanGetName()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getName());

        $mock->setName('test-name');

        self::assertEquals('test-name', $mock->getName());
    }

    /**
     * Tests that can return the short description property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getShortDescription()
     */
    public function testCanGetShortDescription()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getShortDescription());

        $mock->setShortDescription('test-short-description');

        self::assertEquals('test-short-description', $mock->getShortDescription());
    }

    /**
     * Tests that can return the type property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getType()
     */
    public function testCanGetType()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getType());

        $mock->setType('test-type');

        self::assertEquals('test-type', $mock->getType());
    }

    /**
     * Tests that can return the category property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getCategory()
     */
    public function testCanGetCategory()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getCategory());

        $mock->setCategory('test-category');

        self::assertEquals('test-category', $mock->getCategory());
    }

    /**
     * Tests that can get the brand property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getBrand()
     */
    public function testCanGetBrand()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getBrand());

        $mock->setBrand('test-brand');

        self::assertEquals('test-brand', $mock->getBrand());
    }

    /**
     * Tests that can get the version property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getVersion()
     */
    public function testCanGetVersion()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getVersion());

        $mock->setVersion('test-version');

        self::assertEquals('test-version', $mock->getVersion());
    }

    /**
     * Tests that can return the last updated timestamp property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getLastUpdated()
     */
    public function testCanGetLastUpdated()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getLastUpdated());

        $now = time();

        $mock->setLastUpdated($now);

        self::assertEquals($now, $mock->getLastUpdated());
    }

    /**
     * Tests that can return the minimum PHP version property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getMinimumPHPVersion()
     */
    public function testCanGetMinimumPHPVersion()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getMinimumPHPVersion());

        $mock->setMinimumPHPVersion('6.0');

        self::assertEquals('6.0', $mock->getMinimumPHPVersion());
    }

    /**
     * Tests that can return the minimum WordPress version property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getMinimumWordPressVersion()
     */
    public function testCanGetMinimumWordPressVersion()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getMinimumWordPressVersion());

        $mock->setMinimumWordPressVersion('1.0');

        self::assertEquals('1.0', $mock->getMinimumWordPressVersion());
    }

    /**
     * Tests that can return the minimum WooCommerce version property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getMinimumWooCommerceVersion()
     */
    public function testCanGetMinimumWooCommerceVersion()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getMinimumWooCommerceVersion());

        $mock->setMinimumWooCommerceVersion('3.0');

        self::assertEquals('3.0', $mock->getMinimumWooCommerceVersion());
    }

    /**
     * Tests that can return the package URL property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getPackageUrl()
     */
    public function testCanGetPackageUrl()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getPackageUrl());

        $mock->setPackageUrl('https://youtu.be/oHg5SJYRHA0');

        self::assertEquals('https://youtu.be/oHg5SJYRHA0', $mock->getPackageUrl());
    }

    /**
     * Tests that can return the home page URL property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getHomepageUrl()
     */
    public function testCanGetHomepageUrl()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getHomepageUrl());

        $mock->setHomepageUrl('https://youtu.be/oHg5SJYRHA0');

        self::assertEquals('https://youtu.be/oHg5SJYRHA0', $mock->getHomepageUrl());
    }

    /**
     * Tests that can return the documentation URL property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::getDocumentationUrl()
     */
    public function testCanGetDocumentationUrl()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertNull($mock->getDocumentationUrl());

        $mock->setDocumentationUrl('https://youtu.be/oHg5SJYRHA0');

        self::assertEquals('https://youtu.be/oHg5SJYRHA0', $mock->getDocumentationUrl());
    }

    /**
     * Tests that can set the ID property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setId()
     */
    public function testCanSetId()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setId('test-id'));
    }

    /**
     * Tests that can set the slug property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setSlug()
     */
    public function testCanSetSlug()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setSlug('test-slug'));
    }

    /**
     * Tests that can set the name property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setName()
     */
    public function testCanSetName()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setName('test-name'));
    }

    /**
     * Tests that can set the short description property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setShortDescription()
     */
    public function testCanSetShortDescription()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setShortDescription('test-short-description'));
    }

    /** @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setType() */
    public function testCanSetType()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setType('test-type'));
    }

    /**
     * Tests that can set the category property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setCategory()
     */
    public function testCanSetCategory()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setCategory('test-category'));
    }

    /**
     * Tests that can set the brand property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setBrand()
     */
    public function testCanSetBrand()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setVersion('Test Brand'));
    }

    /**
     * Tests that can set the version property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setVersion()
     */
    public function testCanSetVersion()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setVersion('1.2.3'));
    }

    /**
     * Tests that can set the last updated property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setLastUpdated()
     */
    public function testCanSetLastUpdated()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setLastUpdated(time()));
    }

    /**
     * Tests that can set the minimum PHP version property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setMinimumPHPVersion()
     */
    public function testCanSetMinimumPHPVersion()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setMinimumPHPVersion('6.0'));
    }

    /**
     * Tests that can set the minimum WordPress version property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setMinimumWordPressVersion()
     */
    public function testCanSetMinimumWordPressVersion()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setMinimumWordPressVersion('1.0'));
    }

    /**
     * Tests that can set the minimum WooCommerce version property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setMinimumWooCommerceVersion()
     */
    public function testCanSetMinimumWooCommerceVersion()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setMinimumWooCommerceVersion('3.0'));
    }

    /** @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setPackageUrl() */
    public function testCanSetPackageUrl()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setPackageUrl('https://www.youtube.com/watch?v=oHg5SJYRHA0'));
    }

    /**
     * Tests that can set the home page URL property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setHomepageUrl()
     */
    public function testCanSetHomepageUrl()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setHomepageUrl('https://www.youtube.com/watch?v=oHg5SJYRHA0'));
    }

    /**
     * Tests that can set the documentation URL property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::setDocumentationUrl()
     */
    public function testCanSetDocumentationUrl()
    {
        $mock = $this->getMockForAbstractClass(AbstractExtension::class);

        self::assertInstanceOf(AbstractExtension::class, $mock->setDocumentationUrl('https://www.youtube.com/watch?v=oHg5SJYRHA0'));
    }

    /**
     * Tests that can download the extension.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::download()
     */
    public function testCanDownload()
    {
        // test responsibility is delegated to WordPress core functions
        $this->assertConditionsMet();
    }

    /**
     * Tests that an error downloading the extension throws the correct exception.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\AbstractExtension::download()
     */
    public function testDownloadFailed()
    {
        $error = Mockery::mock('WP_Error');
        $error->shouldReceive('get_error_message')->andReturn('error');
        WP_Mock::userFunction('download_url', [
            'times' => 1,
            'return' => $error,
        ]);
        $this->mockStaticMethod(WordPressRepository::class, 'requireWordPressFilesystem')->andReturnNull();

        $this->expectException(ExtensionDownloadFailedException::class);

        $mock = $this->getMockForAbstractClass(AbstractExtension::class);
        $mock->download();
    }
}
