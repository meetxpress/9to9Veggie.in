<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Pages;

use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Pages\AbstractPage;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use PHPUnit\Framework\TestCase;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Pages\AbstractPage
 */
final class AbstractPageTest extends TestCase
{
    /**
     * Tests abstract page constructor.
     *
     * @param string $screenId
     * @param string $pageTitle
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\AbstractPage::__construct()
     * @dataProvider pagesDataProvider
     */
    public function testConstructor(string $screenId, string $pageTitle)
    {
        $instance = $this->getAbstractPageInstance($screenId, $pageTitle);

        $this->assertEquals($screenId, $instance->getScreenId());
        $this->assertEquals($pageTitle, $instance->getPageTitle());
        $this->assertEquals(true, $instance->registerAssetsCalled);
    }

    /**
     * Tests if the current page is the page we want to enqueue the registered assets.
     *
     *
     * @param string $screenId
     * @param string $pageTitle
     * @param string $otherScreenId
     * @throws \ReflectionException
     * @dataProvider pagesDataProvider
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\AbstractPage::shouldEnqueueAssets()
     */
    public function testShouldEnqueueAssets(string $screenId, string $pageTitle, string $otherScreenId)
    {
        $instance = $this->getAbstractPageInstance($screenId, $pageTitle);
        $shouldEnqueueAssetsMethod = TestHelpers::getInaccessibleMethod(get_class($instance), 'shouldEnqueueAssets');

        ArrayHelper::set($GLOBALS, 'pagenow', 'toplevel_page_'.$instance->getScreenId());

        $this->assertEquals(true, $shouldEnqueueAssetsMethod->invoke($instance));

        ArrayHelper::set($GLOBALS, 'pagenow', 'toplevel_page_'.$otherScreenId);

        $this->assertEquals(false, $shouldEnqueueAssetsMethod->invoke($instance));
    }

    /**
     * Tests if should enqueues the necessary assets.
     *
     * @param string $screenId
     * @param string $pageTitle
     * @param string $otherScreenId
     * @throws \ReflectionException
     * @covers \GoDaddy\WordPress\MWC\Common\Pages\AbstractPage::maybeEnqueueAssets()
     * @dataProvider pagesDataProvider
     */
    public function testMaybeEnqueueAssets(string $screenId, string $pageTitle, string $otherScreenId)
    {
        $instance = $this->getAbstractPageInstance($screenId, $pageTitle);

        ArrayHelper::set($GLOBALS, 'pagenow', 'toplevel_page_'.$instance->getScreenId());

        $instance->maybeEnqueueAssets();

        $this->assertEquals(true, $instance->enqueueAssetsCalled);

        $instance = $this->getAbstractPageInstance($screenId, $pageTitle);

        ArrayHelper::set($GLOBALS, 'pagenow', 'toplevel_page_'.$otherScreenId);

        $instance->maybeEnqueueAssets();

        $this->assertEquals(false, $instance->enqueueAssetsCalled);
    }

    /**
     * Gets an instance implementing the abstract page.
     *
     * @param string $screenId
     * @param string $pageTitle
     * @return AbstractPage
     */
    private function getAbstractPageInstance(string $screenId, string $pageTitle) : AbstractPage
    {
        return new class($screenId, $pageTitle) extends AbstractPage {
            public $registerAssetsCalled = false;
            public $enqueueAssetsCalled = false;

            public function registerAssets()
            {
                parent::registerAssets();

                $this->registerAssetsCalled = true;
            }

            public function getScreenId() : string
            {
                return $this->screenId;
            }

            public function getPageTitle() : string
            {
                return $this->pageTitle;
            }

            protected function enqueueAssets()
            {
                $this->enqueueAssetsCalled = true;
            }
        };
    }

    /** @see AbstractPageTest tests */
    public function pagesDataProvider() : array
    {
        return [
            ['some_page', 'Some Page Title', 'other_page'],
            ['other_page', 'Other Page Title', 'some_page'],
        ];
    }
}
