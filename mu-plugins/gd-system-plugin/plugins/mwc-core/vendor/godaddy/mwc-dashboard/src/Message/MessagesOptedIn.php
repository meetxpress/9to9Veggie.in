<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Message;

use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Traits\HasUserMetaTrait;

/**
 * Holds the user preference regarding MWC Dashboard messages (opted in or opted out).
 */
class MessagesOptedIn
{
    use HasUserMetaTrait;

    /**
     * Class constructor.
     *
     * @since x.y.z
     *
     * @param int|null $userId
     *
     * @since x.y.z
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->metaKey = '_mwc_dashboard_messages_opted_in';

        // defaults to false because merchants are opted out by default
        $this->loadUserMeta(false);
    }

    /**
     * Opts in the user for the Dashboard messages.
     *
     * @since x.y.z
     */
    public function optIn()
    {
        $this->setUserMeta(true);
        $this->saveUserMeta();
    }

    /**
     * Opts out the user for the Dashboard messages.
     *
     * @since x.y.z
     */
    public function optOut()
    {
        $this->setUserMeta(false);
        $this->saveUserMeta();
    }

    /**
     * Gets the ID of the user associated with this preference.
     *
     * @since x.y.z
     *
     * @return int
     */
    public function getUserId() : int
    {
        return $this->userId;
    }
}
