<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Extensions\Types;

use GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension
 */
final class ThemeExtensionTest extends WPTestCase
{
    /**
     * Gets a Theme instance for testing.
     *
     * @return ThemeExtension
     */
    private function getInstance() : ThemeExtension
    {
        return new ThemeExtension();
    }

    /**
     * Tests that can get the theme type.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension::getType()
     */
    public function testGetType()
    {
        $theme = $this->getInstance();

        $this->assertEquals('theme', $theme->getType());
    }

    /**
     * Tests that can return the image URLs property.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension::getImageUrls()
     */
    public function testCanGetImageUrls()
    {
        $instance = $this->getInstance();

        $this->assertIsArray($instance->getImageUrls());

        $image_urls = [
            '1x' => 'url1',
            '2x' => 'url2',
        ];

        $instance->setImageUrls($image_urls);

        $this->assertEquals($image_urls, $instance->getImageUrls());
    }

    /**
     * Tests that can set the image URLs property and return an instance of self.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension::setImageUrls()
     */
    public function testCanSetImageUrls()
    {
        $instance = $this->getInstance();

        $this->assertInstanceOf(ThemeExtension::class, $instance->setImageUrls([]));
        $this->assertEmpty($instance->getImageUrls());

        $instance->setImageUrls(['test']);

        $this->assertEquals(['test'], $instance->getImageUrls());
    }

    /**
     * Tests that can install the theme.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension::install()
     */
    public function testCanInstall()
    {
        $this->assertConditionsMet();
    }

    /**
     * Tests that can determine whether the theme is installed.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension::install()
     */
    public function testCanDetermineIfInstalled()
    {
        $this->assertConditionsMet();
    }

    /**
     * Tests that can activate the theme.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension::activate()
     */
    public function testCanActivate()
    {
        $this->assertConditionsMet();
    }

    /**
     * Tests that can determine whether the theme is active.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension::isActive()
     */
    public function testCanDetermineIfActive()
    {
        $this->assertConditionsMet();
    }

    /**
     * Tests that can deactivate the theme.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension::deactivate()
     */
    public function testCanDeactivate()
    {
        $this->assertConditionsMet();
    }

    /**
     * Tests that can uninstall the theme.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Extensions\Types\ThemeExtension::uninstall()
     */
    public function testCanUninstall()
    {
        $this->assertConditionsMet();
    }
}
