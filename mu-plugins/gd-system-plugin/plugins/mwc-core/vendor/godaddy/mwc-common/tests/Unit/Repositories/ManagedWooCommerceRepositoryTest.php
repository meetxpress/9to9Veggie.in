<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Repositories;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Tests\TestHelpers;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use ReflectionException;
use ReflectionMethod;
use WP_Mock;
use function Patchwork\redefine;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository
 */
class ManagedWooCommerceRepositoryTest extends WPTestCase
{
    /**
     * Tests that the current managed wordpress environment can be determined.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::getEnvironment()
     * @throws Exception
     */
    public function testCanGetEnvironment()
    {
        Configuration::set('godaddy.is_staging_site', true);

        $this->assertEquals('staging', ManagedWooCommerceRepository::getEnvironment());

        Configuration::set('godaddy.is_staging_site', false);
        Configuration::set('mwc.mwp_settings.cname_link', []);

        $this->assertEquals('development', ManagedWooCommerceRepository::getEnvironment());

        Configuration::set('mwc.mwp_settings.cname_link', 'test');
        Configuration::set('mwc.env', 'preset-env');

        $this->assertEquals('preset-env', ManagedWooCommerceRepository::getEnvironment());
    }

    /**
     * Tests that the current managed wordpress environment can be determined.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::isStagingEnvironment()
     * @throws Exception
     */
    public function testCanCheckIsStagingEnvironment()
    {
        redefine(ManagedWooCommerceRepository::class.'::getEnvironment', function() {
            return 'staging';
        });

        $this->assertTrue(ManagedWooCommerceRepository::isStagingEnvironment());

        redefine(ManagedWooCommerceRepository::class.'::getEnvironment', function() {
            return 'production';
        });

        $this->assertFalse(ManagedWooCommerceRepository::isStagingEnvironment());
    }

    /**
     * Tests that the current managed wordpress environment can be determined.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::isProductionEnvironment()
     * @throws Exception
     */
    public function testCanCheckIsProductionEnvironment()
    {
        redefine(ManagedWooCommerceRepository::class.'::getEnvironment', function() {
            return 'production';
        });

        $this->assertTrue(ManagedWooCommerceRepository::isProductionEnvironment());

        redefine(ManagedWooCommerceRepository::class.'::getEnvironment', function() {
            return 'staging';
        });

        $this->assertFalse(ManagedWooCommerceRepository::isProductionEnvironment());
    }

    /**
     * Tests that the current managed wordpress environment can be determined.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::isLocalEnvironment()
     * @throws Exception
     */
    public function testCanCheckIsLocalEnvironment()
    {
        redefine(ManagedWooCommerceRepository::class.'::getEnvironment', function() {
            return 'development';
        });

        $this->assertTrue(ManagedWooCommerceRepository::isLocalEnvironment());

        redefine(ManagedWooCommerceRepository::class.'::getEnvironment', function() {
            return 'staging';
        });

        $this->assertFalse(ManagedWooCommerceRepository::isLocalEnvironment());
    }

    /**
     * Tests that the current managed wordpress environment can be determined.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::isTestingEnvironment()
     * @throws Exception
     */
    public function testCanCheckIsTestingEnvironment()
    {
        redefine(ManagedWooCommerceRepository::class.'::getEnvironment', function() {
            return 'testing';
        });

        $this->assertTrue(ManagedWooCommerceRepository::isTestingEnvironment());

        redefine(ManagedWooCommerceRepository::class.'::getEnvironment', function() {
            return 'staging';
        });

        $this->assertFalse(ManagedWooCommerceRepository::isTestingEnvironment());
    }

    /**
     * Test that it can determine whether a site is a managed WordPress hosted site.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::isManagedWordPress()
     * @throws Exception
     */
    public function testCanDetermineIsManagedWordPressHostedSite()
    {
        $this->mockWordPressTransients();

        Configuration::set('godaddy.account.uid', null);

        $this->assertFalse(ManagedWooCommerceRepository::isManagedWordPress());

        Configuration::set('godaddy.account.uid', '12345');

        $this->assertTrue(ManagedWooCommerceRepository::isManagedWordPress());
    }

    /**
     * Tests whether it can determine if the current sit has an Ecommerce plan.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::hasEcommercePlan()
     * @throws Exception
     */
    public function testCanCheckHasEcommercePlan()
    {
        $this->mockWordPressTransients();

        $path = StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'tests');

        Configuration::set('godaddy.account.uid', 123456);
        Configuration::set('wordpress.absolute_path', $path);
        Configuration::set('mwc.plan_name', 'pass');
        Configuration::set('godaddy.account.plan.name', 'fail');
        Configuration::set('wordpress.absolute_path', 'foo/bar');

        $this->assertFalse(ManagedWooCommerceRepository::hasEcommercePlan());

        Configuration::set('godaddy.account.uid', 123456);
        Configuration::set('wordpress.absolute_path', $path);
        Configuration::set('godaddy.account.plan.name', 'pass');

        $this->assertTrue(ManagedWooCommerceRepository::hasEcommercePlan());
    }

    /**
     * Tests that can get a reseller account ID from configuration.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::getResellerId()
     * @throws Exception
     */
    public function testCanGetResellerId()
    {
        $this->mockWordPressTransients();

        Configuration::set('godaddy.reseller', null);

        $this->assertNull(ManagedWooCommerceRepository::getResellerId());

        Configuration::set('godaddy.reseller', 12345678);

        $this->assertEquals(12345678, ManagedWooCommerceRepository::getResellerId());
    }

    /**
     * Tests that can determine whether the current is a reseller account/instance.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::isReseller()
     * @throws Exception
     */
    public function testCanDetermineIsReseller()
    {
        $this->mockWordPressTransients();

        Configuration::set('godaddy.account.uid', '123');
        Configuration::set('godaddy.reseller', null);

        $this->assertFalse(ManagedWooCommerceRepository::isReseller());

        Configuration::set('godaddy.account.uid', 'ABC');
        Configuration::set('godaddy.reseller', 12345678);

        $this->assertTrue(ManagedWooCommerceRepository::isReseller());
    }

    /**
     * Tests that can determine whether the current is a reseller account/instance with support agreement.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::isResellerWithSupportAgreement()
     *
     * @param bool $isReseller expected result
     * @param int|null $resellerId reseller ID
     * @param bool $customerSupportOptOut value for the customerSupportOptOut field
     *
     * @dataProvider providerCanDetermineIsResellerWithSupportAgreement
     * @throws Exception
     */
    public function testCanDetermineIsResellerWithSupportAgreement(bool $isReseller, $resellerId, bool $customerSupportOptOut)
    {
        $this->mockWordPressTransients();

        Configuration::set('mwc.extensions.api.url', 'https://example.org/storefront/v1');
        Configuration::set('mwc.extensions.api.settings.reseller.endpoint', 'settings');

        // ensure isManagedWordPress() returns true
        Configuration::set('godaddy.account.uid', 1234);

        // mock Storefront API > Setting response
        $this->mockWordPressRequestFunctionsWithArgs([
            'url'      => 'https://example.org/storefront/v1/settings',
            'response' => [
                'code' => 200,
                'body' => [
                    'progId'                => 'domainspricedright',
                    'resellerTypeId'        => 2,
                    'customerSupportOptOut' => $customerSupportOptOut,
                    'privateLabelId'        => $resellerId,
                ],
            ],
        ]);

        Configuration::set('godaddy.reseller', $resellerId);

        $this->assertSame($isReseller, ManagedWooCommerceRepository::isResellerWithSupportAgreement());
    }

    /** @see testCanDetermineIsResellerWithSupportAgreement() */
    public function providerCanDetermineIsResellerWithSupportAgreement()
    {
        return [
            'no reseller configured'    => [false, null, false],
            'no reseller no agreement'  => [false, null, true],
            'reseller with agreement'   => [true, 123456, false],
            'reseller but no agreement' => [false, 123456, true],
        ];
    }

    /**
     * Tests that can get settings storefront API.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::getStorefrontSettingsApiUrl()
     * @throws ReflectionException|Exception
     */
    public function testCanGetStorefrontAPIUrl()
    {
        Configuration::set('mwc.extensions.api.url', 'base');
        Configuration::set('mwc.extensions.api.settings.reseller.endpoint', 'settings');

        $class = new ManagedWooCommerceRepository();
        $method = TestHelpers::getInaccessibleMethod(ManagedWooCommerceRepository::class, 'getStorefrontSettingsApiUrl');

        $this->assertEquals('base/settings', $method->invoke($class));

        Configuration::set('mwc.extensions.api.url', 'base/');

        $this->assertEquals('base/settings', $method->invoke($class));
    }

    /**
     * Tests that can get reseller settings.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::getResellerSettings()
     * @throws ReflectionException
     */
    public function testCanGetResellerSettings()
    {
        $this->mockWordPressTransients();
        $this->mockWordPressRequestFunctions();
        $this->mockWordPressResponseFunctions();

        $method = TestHelpers::getInaccessibleMethod(ManagedWooCommerceRepository::class, 'getResellerSettings');

        $this->assertIsArray($method->invoke(new ManagedWooCommerceRepository(), 123, []));
    }

    /**
     * Tests that can determine if using a temporary domain.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::isTemporaryDomain()
     * @throws Exception
     */
    public function testCanDetermineIfTemporaryDomain()
    {
        $this->mockWordPressTransients();

        $path = StringHelper::trailingSlash(StringHelper::before(__DIR__, 'tests').'tests');

        Configuration::set('godaddy.account.uid', 123456);
        Configuration::set('wordpress.absolute_path', $path);
        Configuration::set('godaddy.temporary_domain', 'fail');

        $this->assertFalse(ManagedWooCommerceRepository::isTemporaryDomain());

        Configuration::set('godaddy.temporary_domain', '/');

        $this->assertTrue(ManagedWooCommerceRepository::isTemporaryDomain());
    }

    /**
     * Tests that whether the WPNux onboarding has not been completed.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::hasCompletedWPNuxOnboarding()
     */
    public function testCanDetermineHasNotCompletedWPNuxOnboarding()
    {
        $this->mockWordPressGetOption('wpnux_imported', false);
        $this->assertFalse(ManagedWooCommerceRepository::hasCompletedWPNuxOnboarding());
    }

    /**
     * Tests that whether the WPNux onboarding has been completed.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::hasCompletedWPNuxOnboarding()
     * @throws Exception
     */
    public function testCanDetermineHasCompletedWPNuxOnboarding()
    {
        $this->mockStaticMethod(WordPressRepository::class, 'hasWordPressInstance')->andReturn(true);
        $this->mockWordPressGetOption('wpnux_imported', true);

        $this->assertTrue(ManagedWooCommerceRepository::hasCompletedWPNuxOnboarding());
    }

    /**
     * Test that can get the value of the XID server variable.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::getXid()
     */
    public function testCanGetXid()
    {
        $_SERVER['XID'] = 9876543;

        $this->assertSame($_SERVER['XID'], ManagedWooCommerceRepository::getXid());

        $_SERVER['XID'] = 1000;

        $this->assertEquals(0, ManagedWooCommerceRepository::getXid());
    }

    /**
     * Test that can get Site ID.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository::getSiteId()
     * @throws Exception
     */
    public function testCanGetSiteId()
    {
        WP_Mock::userFunction('get_option');
        WP_Mock::userFunction('update_option')->with('gd_mwc_site_id', 9876543)->once();

        $_SERVER['XID'] = 9876543;

        $this->assertSame((string) $_SERVER['XID'], ManagedWooCommerceRepository::getSiteId());
        $this->assertSame((string) $_SERVER['XID'], Configuration::get('godaddy.site.id'));

        Configuration::set('godaddy.site.id', '888888');

        $this->assertSame(Configuration::get('godaddy.site.id'), ManagedWooCommerceRepository::getSiteId());
    }
}
