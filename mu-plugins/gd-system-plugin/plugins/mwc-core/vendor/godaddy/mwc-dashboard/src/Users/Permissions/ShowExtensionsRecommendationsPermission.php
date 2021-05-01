<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Users\Permissions;

use GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait;

/**
 * Handles the permission to show extensions recommendations to a user.
 *
 * @since x.y.z
 */
class ShowExtensionsRecommendationsPermission
{
    use HasUserMetaTrait;

    /**
     * ShowExtensionsRecommendationsPermission constructor.
     *
     * @since x.y.z
     *
     * @param int $userId user ID used to load/store the metadata
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;

        $this->metaKey = '_mwc_marketing_permissions_show_extensions_recommendations';

        $this->value = $this->loadUserMeta('yes');
    }

    /**
     * Allows marketing permissions to show extensions.
     *
     * @since x.y.z
     *
     * @return $this
     */
    public function allow() : self
    {
        return $this
            ->setUserMeta('yes')
            ->saveUserMeta();
    }

    /**
     * Disallows marketing permissions to show extensions.
     *
     * @since x.y.z
     *
     * @return $this
     */
    public function disallow() : self
    {
        return $this
            ->setUserMeta('no')
            ->saveUserMeta();
    }

    /**
     * Determines whether the user has marketing permissions to see extensions.
     *
     * @since x.y.z
     *
     * @return bool
     */
    public function isAllowed() : bool
    {
        return 'yes' === $this->getUserMeta() && user_can($this->userId, 'install_plugins') && user_can($this->userId, 'activate_plugins');
    }
}
