<?php

namespace GoDaddy\WordPress\MWC\Common\DataSources\WordPress\Adapters;

use Exception;
use GoDaddy\WordPress\MWC\Common\DataSources\Contracts\DataSourceAdapterContract;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\DeprecationHelper;
use WP_Screen;

/**
 * WordPress Screen Adapter class.
 *
 * @since x.y.z
 */
class WordPressScreenAdapter implements DataSourceAdapterContract
{
    /**
     * The WordPress screen object.
     *
     * @since x.y.z
     *
     * @var WP_Screen
     */
    protected $screen;

    /**
     * WordPressScreenAdapter constructor.
     *
     * @since x.y.z
     *
     * @param WP_Screen $screen
     */
    public function __construct(WP_Screen $screen)
    {
        $this->screen = $screen;
    }

    /**
     * Gets the data for the post list page.
     *
     * @since x.y.z
     *
     * @return array
     */
    protected function getPostListPageData() : array
    {
        $pageId = "{$this->getNormalizedPostType()}_list";

        return [
            'pageId'       => $pageId,
            'pageContexts' => [$pageId],
        ];
    }

    /**
     * Gets the data for the add post page.
     *
     * @since x.y.z
     *
     * @return array
     */
    protected function getAddPostPageData() : array
    {
        $pageId = "add_{$this->getNormalizedPostType()}";

        return [
            'pageId'       => $pageId,
            'pageContexts' => [$pageId],
        ];
    }

    /**
     * Gets the data for the edit post page.
     *
     * @since x.y.z
     *
     * @return array
     */
    protected function getEditPostPageData() : array
    {
        $postId = (string) ArrayHelper::get($_REQUEST, 'post', '');

        return [
            'pageId'       => "edit_{$this->getNormalizedPostType()}",
            'pageContexts' => ["edit_{$this->getNormalizedPostType()}"],
            'objectId'     => $postId,
            'objectType'   => $this->getNormalizedPostType(),
            'objectStatus' => $this->getNormalizedPostStatus($postId),
        ];
    }

    /**
     * Builds the page data array for a given WooCommerce page.
     *
     * @since x.y.z
     *
     * @param string $page
     * @param string $tab
     * @param string $section
     *
     * @return array[]
     */
    protected function getWooCommercePageData(string $page, $tab, $section) : array
    {
        return [
            'pageId'       => $this->getWooCommercePageId($page, $tab, $section),
            'pageContexts' => $this->getWooCommercePageContexts($page, $tab, $section),
        ];
    }

    /**
     * Gets the data for the WooCommerce settings page.
     *
     * @since x.y.z
     *
     * @return array[]
     */
    protected function getWooCommerceSettingsPageData() : array
    {
        $tab = ArrayHelper::get($_REQUEST, 'tab', '');
        $section = ArrayHelper::get($_REQUEST, 'section', '');

        return $this->getWooCommercePageData('settings', $tab, $section);
    }

    /**
     * Gets the data for the WooCommerce admin page.
     *
     * @since x.y.z
     *
     * @return array[]
     */
    protected function getWooCommerceAdminPageData() : array
    {
        $path = explode('/', urldecode(ArrayHelper::get($_REQUEST, 'path', '')));

        $tab     = $path[1] ?? '';
        $section = $path[2] ?? '';

        return $this->getWooCommercePageData('admin', $tab, $section);
    }

    /**
     * Gets the data for a generic page.
     *
     * @since x.y.z
     *
     * @return array
     */
    protected function getGenericPageData() : array
    {
        return [
            'pageId'       => $this->screen->base,
            'pageContexts' => [$this->screen->base],
        ];
    }

    /**
     * Gets the WooCommerce settings page ID.
     *
     * @since x.y.z
     *
     * @param string $tab
     * @param string $section
     *
     * @return mixed
     *
     * @throws Exception
     *
     * @deprecated
     */
    protected function getWooCommerceSettingsPageId(string $tab, string $section)
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z', static::class.'::getWooCommercePageId');

        return $this->getWooCommercePageId('settings', $tab, $section);
    }

    /**
     * Gets the WooCommerce page ID.
     *
     * @since x.y.z
     *
     * @param string $page
     * @param string $tab
     * @param string $section
     *
     * @return mixed
     */
    protected function getWooCommercePageId(string $page, string $tab, string $section)
    {
        $pageContexts = $this->getWooCommercePageContexts($page, $tab, $section);

        return end($pageContexts);
    }

    /**
     * Gets WooCommerce settings page contexts.
     *
     * @since x.y.z
     *
     * @param string $tab
     * @param string $section
     *
     * @return array
     *
     * @throws Exception
     *
     * @deprecated
     */
    protected function getWooCommerceSettingsPageContexts(string $tab, string $section) : array
    {
        DeprecationHelper::deprecatedFunction(__METHOD__, 'x.y.z', static::class.'::getWooCommercePageContexts');

        return $this->getWooCommercePageContexts('settings', $tab, $section);
    }

    /**
     * Gets WooCommerce page contexts.
     *
     * @since x.y.z
     *
     * @param string $page
     * @param string $tab
     * @param string $section
     *
     * @return array
     */
    protected function getWooCommercePageContexts(string $page, string $tab, string $section) : array
    {
        $contexts = ["woocommerce_{$page}"];

        if (! empty($tab)) {
            $contexts[] = "woocommerce_{$page}_{$tab}";

            if (! empty($section)) {
                $contexts[] = "woocommerce_{$page}_{$tab}_{$section}";
            }
        }

        return $contexts;
    }

    /**
     * Gets the normalized post type for the current screen.
     *
     * @since x.y.z
     *
     * @return string
     */
    protected function getNormalizedPostType() : string
    {
        return str_replace('shop_', '', (string) $this->screen->post_type);
    }

    /**
     * Gets the normalized post status for the current screen.
     *
     * @since x.y.z
     *
     * @param string $postId
     *
     * @return string
     */
    protected function getNormalizedPostStatus(string $postId) : string
    {
        return str_replace('wc-', '', (string) get_post_status($postId));
    }

    /**
     * Converts from Data Source format.
     *
     * @since x.y.z
     *
     * @return array
     */
    public function convertFromSource() : array
    {
        switch ($this->screen->base) {
            case 'edit':
                return $this->getPostListPageData();
            case 'post':
                return $this->screen->action === 'add' ? $this->getAddPostPageData() : $this->getEditPostPageData();
            case 'woocommerce_page_wc-settings':
                return $this->getWooCommerceSettingsPageData();
            case 'woocommerce_page_wc-admin':
                return $this->getWooCommerceAdminPageData();
        }

        return $this->getGenericPageData();
    }

    /**
     * Converts to Data Source format.
     *
     * @since x.y.z
     *
     * @return array
     */
    public function convertToSource() : array
    {
        return [];
    }
}
