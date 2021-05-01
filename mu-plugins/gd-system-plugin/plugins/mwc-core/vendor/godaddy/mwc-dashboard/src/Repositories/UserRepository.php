<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Repositories;

use GoDaddy\WordPress\MWC\Common\Exceptions\BaseException;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Dashboard\Message\MessagesOptedIn;

/**
 * User repository handler.
 *
 * @deprecated Consider use of the upcoming standard User object
 */
class UserRepository
{
    /**
     * Returns the user full name (if set) or login.
     *
     * @param \WP_User|null $user
     * @return string
     */
    public static function getUserName(\WP_User $user = null) : string
    {
        if (empty($user)) {
            $user = WordPressRepository::getUser();
        }

        $name = trim(implode(' ', [$user->get('first_name'), $user->get('last_name')]));

        if (empty($name)) {
            // fallback to login, if first and last name are not set
            $name = $user->user_nicename;
        }

        return $name;
    }

    /**
     * Returns the password reset URL for the given user.
     *
     * @param \WP_User|null $user
     * @return string
     * @throws BaseException
     */
    public static function getPasswordResetUrl(\WP_User $user = null): string
    {
        if (empty($user)) {
            $user = WordPressRepository::getUser();
        }

        $passwordResetKey = get_password_reset_key($user);

        if (is_wp_error($passwordResetKey)) {
            throw new BaseException($passwordResetKey->get_error_message());
        }

        $userLogin = $user->user_login;

        return network_site_url("wp-login.php?action=rp&key=$passwordResetKey&login=".rawurlencode($userLogin), 'login');
    }

    /**
     * Checks if the user has opted in to receive MWC Dashboard messages.
     *
     * @return bool
     */
    public static function userOptedInForDashboardMessages(): bool
    {
        return (bool) (new MessagesOptedIn(WordPressRepository::getCurrentUserId()))->getUserMeta();
    }
}
