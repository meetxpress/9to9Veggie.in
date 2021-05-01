<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Pages;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Enqueue\Enqueue;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Pages\WordPressAdminPage;
use GoDaddy\WordPress\MWC\Common\Register\Register;

/**
 * Class Dashboard page.
 *
 * @since x.y.z
 */
class GetHelpPage extends WordPressAdminPage
{
    /** @var string ID of the div element inside which the page will be rendered */
    protected $divId;

    /** @var string String of styles to apply to the div element */
    protected $divStyles;

    /**
     * GetHelpPage constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        // page ID and title
        $this->screenId = 'mwc-get-help';
        $this->pageTitle = __('Get Help', 'mwc-dashboard');
        $this->parentMenuSlug = $this->screenId;

        // minimum capability required
        $this->capability = 'manage_options';

        $this->divId = 'mwc-dashboard';
        $this->divStyles = 'margin-left: -20px';

        Register::action()
            ->setGroup('current_screen')
            ->setHandler([$this, 'hideNotices'])
            ->execute();

        parent::__construct($this->screenId, $this->pageTitle);
    }

    /**
     * Renders the page HTML.
     *
     * @since x.y.z
     */
    public function render()
    {
        ?>
        <div id="<?php echo $this->divId; ?>" style="<?php echo $this->divStyles; ?>"></div>
        <?php
    }

    /**
     * Checks if the current opened page is the Dashboard page.
     *
     * @since x.y.z
     *
     * @return bool
     */
    protected function isGetHelpPage() : bool
    {
        global $current_screen;

        if (! $current_screen) {
            return false;
        }

        $matches = [
            'toplevel_page_'.$this->screenId,
            'skyverge_page_'.$this->screenId,
        ];

        return ArrayHelper::contains($matches, $current_screen->id);
    }

    /**
     * Checks if assets should be enqueued or not.
     *
     * @since x.y.z
     *
     * @return bool
     */
    protected function shouldEnqueueAssets() : bool
    {
        return $this->isGetHelpPage();
    }

    /**
     * Enqueues/loads registered assets.
     *
     * @since x.y.z
     *
     * @throws Exception
     */
    protected function enqueueAssets()
    {
        Enqueue::style()
            ->setHandle("{$this->divId}-fonts")
            ->setSource(Configuration::get('mwc_dashboard.assets.css.fonts.url'))
            ->execute();

        parent::enqueueAssets();
    }

    /**
     * Renders the style tag and opens a div wrap with the class to hide the notices.
     *
     * @since x.y.z
     *
     * @internal
     */
    public function injectBeforeNotices()
    {
        if (! $this->isGetHelpPage()) {
            return;
        }

        echo '<style type="text/css">.skyverge-dashboard-hidden { display: none !important; } </style>',
        '<div class="skyverge-dashboard-hidden">',
        '<div class="wp-header-end"></div>';
    }

    /**
     * Closes the div wrap with the class to hide the notices.
     *
     * @since x.y.z
     *
     * @internal
     */
    public function injectAfterNotices()
    {
        if (! $this->isGetHelpPage()) {
            return;
        }

        echo '</div>';
    }

    /**
     * Wraps page notices with hidden elements to hide all notices.
     *
     * @since x.y.z
     *
     * @throws Exception
     */
    public function hideNotices()
    {
        if (! $this->isGetHelpPage()) {
            return;
        }

        Register::action()
            ->setGroup('admin_notices')
            ->setHandler([$this, 'injectBeforeNotices'])
            ->setPriority(-9999)
            ->execute();

        Register::action()
            ->setGroup('admin_notices')
            ->setHandler([$this, 'injectAfterNotices'])
            ->setPriority(PHP_INT_MAX)
            ->execute();
    }

    /**
     * Registers the menu page.
     *
     * @since x.y.z
     *
     * @internal
     *
     * @return WordPressAdminPage
     */
    public function addMenuItem() : WordPressAdminPage
    {
        add_submenu_page(
            $this->parentMenuSlug,
            $this->pageTitle,
            $this->pageTitle.'<div id="mwc-dashboard-menu-item"></div>',
            $this->capability,
            $this->screenId,
            [$this, 'render']
        );

        return $this;
    }
}
