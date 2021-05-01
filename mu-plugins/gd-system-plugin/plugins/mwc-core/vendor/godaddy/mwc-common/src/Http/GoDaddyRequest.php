<?php

namespace GoDaddy\WordPress\MWC\Common\Http;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;

/**
 * Request handler for performing requests to GoDaddy.
 *
 * This class also wraps a Managed WooCommerce Site Token required by GoDaddy requests.
 *
 * @since 1.0.0
 */
class GoDaddyRequest extends Request
{
    /** @var string managed WooCommerce site token */
    public $siteToken;

    /**
     * Constructor.
     *
     * @since 1.0.0
     *
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->siteToken()->headers(['X-Site-Token' => $this->siteToken]);
    }

    /**
     * Sets the current site API request token.
     *
     * @since 1.0.0
     *
     * @param string|null $token
     * @return GoDaddyRequest
     * @throws Exception
     */
    public function siteToken($token = null) : GoDaddyRequest
    {
        $this->siteToken = $token ?: Configuration::get('godaddy.site.token', 'empty');

        return $this;
    }
}
