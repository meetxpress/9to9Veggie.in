<?php

namespace GoDaddy\WordPress\MWC\Dashboard;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Plugin\BasePlatformPlugin;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;
use GoDaddy\WordPress\MWC\Common\Traits\IsSingletonTrait;
use GoDaddy\WordPress\MWC\Dashboard\API\API;
use GoDaddy\WordPress\MWC\Dashboard\Menu\GetHelpMenu;
use GoDaddy\WordPress\MWC\Dashboard\Pages\WooCommerceExtensionsPage;

/**
 * MWC Dashboard class.
 *
 * @since x.y.z
 *
 * @method static \GoDaddy\WordPress\MWC\Dashboard\Dashboard getInstance()
 */
final class Dashboard extends BasePlatformPlugin
{
    use IsSingletonTrait;

    /**
     * Plugin name.
     *
     * @since x.y.z
     *
     * @var string
     */
    protected $name = 'MWC Dashboard';

    /**
     * Classes to instantiate.
     *
     * @since x.y.z
     *
     * @var array
     */
    protected $classesToInstantiate = [];

    /**
     * Plugin constructor.
     *
     * @since x.y.z
     *
     * @throws Exception
     */
    public function __construct()
    {
        // needed to load the configurations so we can use them for the following check
        parent::__construct();

        // should not load anything for non-MWC sites
        if (! ManagedWooCommerceRepository::hasEcommercePlan()) {
            return;
        }

        load_plugin_textdomain('mwc-dashboard', false, dirname(plugin_basename(__FILE__)).'/languages');

        if ($this->shouldDisplayGetHelpPage()) {
            $this->deactivateSkyVergeDashboard();
        }
    }

    /**
     * Initializes the Configuration class adding the plugin's configuration directory.
     *
     * @since x.y.z
     */
    protected function initializeConfiguration()
    {
        Configuration::initialize(StringHelper::trailingSlash(StringHelper::before(__DIR__, 'src').'configurations'));
    }

    /**
     * Gets configuration values.
     *
     * @since x.y.z
     *
     * @return array
     */
    protected function getConfigurationValues() : array
    {
        $configurationValues = parent::getConfigurationValues();

        $configurationValues['PLUGIN_DIR'] = StringHelper::before(__DIR__, 'src');
        $configurationValues['PLUGIN_URL'] = StringHelper::before(plugin_dir_url(__FILE__), 'src');
        $configurationValues['VERSION'] = 'x.y.z';

        return $configurationValues;
    }

    /**
     * Gets the classes that should be instantiated when initializing the inheriting plugin.
     *
     * @return array
     * @since x.y.z
     *
     * @throws \Exception
     */
    protected function getClassesToInstantiate() : array
    {
        // should not instantiate anything for non-MWC sites
        if (ManagedWooCommerceRepository::hasEcommercePlan()) {
            $this->classesToInstantiate = [
                API::class                       => true,
                GetHelpMenu::class               => $this->shouldDisplayGetHelpPage() ? 'web' : false,
                WooCommerceExtensionsPage::class => 'web',
            ];
        }

        return $this->classesToInstantiate;
    }

    /**
     * Deactivates SkyVerge Dashboard plugin completely.
     *
     * @since x.y.z
     *
     * @throws Exception
     */
    protected function deactivateSkyVergeDashboard()
    {
        $this->deactivateSkyVergeDashboardPlugin();
        $this->stopBundledSkyVergeDashboardFromLoading();
    }

    /**
     * Makes sure to prevent bundled SkyVerge dashboard from loading.
     *
     * @since x.y.z
     *
     * @throws Exception
     */
    protected function stopBundledSkyVergeDashboardFromLoading()
    {
        Register::action()
            ->setGroup('plugins_loaded')
            ->setPriority(1)
            ->setHandler([$this, 'unhookBundledSkyVergeDashboard'])
            ->execute();
    }

    /**
     * Unhooks bundled SkyVerge Dashboard initialization.
     *
     * @internal
     *
     * @since x.y.z
     *
     * @throws Exception
     */
    public function unhookBundledSkyVergeDashboard()
    {
        if (class_exists('SkyVerge_Dashboard_Loader')) {
            Register::action()
                ->setGroup('plugins_loaded')
                ->setHandler([SkyVerge_Dashboard_Loader::instance(), 'init_plugin'])
                ->deregister();
        }
    }

    /**
     * Checks if the SkyVerge Dashboard plugin is active and deactivates it.
     *
     * @since x.y.z
     */
    protected function deactivateSkyVergeDashboardPlugin()
    {
        if (! class_exists('SkyVerge_Dashboard_Loader')) {
            return;
        }

        deactivate_plugins('skyverge-dashboard/skyverge-dashboard.php');

        try {
            $this->displayAdminNoticeForSkyVergeDashboardPlugin();
        } catch (Exception $ex) {
            // @TODO maybe upon error, display and notice in some other way {NM 2021-01-08}
        }
    }

    /**
     * Displays admin notice upon deactivating the SkyVerge Dashboard plugin.
     *
     * @since x.y.z
     *
     * @throws Exception
     */
    protected function displayAdminNoticeForSkyVergeDashboardPlugin()
    {
        Register::action()
            ->setGroup('admin_notices')
            ->setHandler([$this, 'renderAdminNoticeForSkyVergeDashboardPlugin'])
            ->execute();
    }

    /**
     * Renders admin notice upon deactivating the SkyVerge Dashboard plugin.
     *
     * @internal
     *
     * @since x.y.z
     */
    public function renderAdminNoticeForSkyVergeDashboardPlugin()
    {
        echo '<div class="notice notice-info is-dismissible"><p>',
        __('<strong>Heads up!</strong> We\'ve deactivated the SkyVerge Dashboard plugin since you now have access to the dashboard via the Get Help menu!',
            'mwc-dashboard'),
        '</p></div>';
    }

    /**
     * Checks if the MWC Get Help page should be displayed.
     *
     * It should never be displayed for resellers' sites who opted out of support.
     *
     * @since x.y.z
     *
     * @return bool
     *
     * @throws Exception
     */
    protected function shouldDisplayGetHelpPage() : bool
    {
        // should not display if Dashboard is disabled through configuration
        if (! Configuration::get('features.mwc_dashboard')) {
            return false;
        }

        // should not display for non-MWC sites
        if (! ManagedWooCommerceRepository::hasEcommercePlan()) {
            return false;
        }

        // display the dashboard for end-customers (non-resellers) or resellers with a support agreement
        return ! ManagedWooCommerceRepository::isReseller() ||
            ManagedWooCommerceRepository::isResellerWithSupportAgreement();
    }
}
