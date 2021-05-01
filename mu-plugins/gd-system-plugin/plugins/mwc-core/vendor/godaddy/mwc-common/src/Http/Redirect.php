<?php

namespace GoDaddy\WordPress\MWC\Common\Http;

use Exception;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

/**
 * Redirection handler.
 *
 * @since 1.0.0
 */
class Redirect
{
    /** @var object|array the query parameters */
    public $queryParameters;

    /** @var string the path to redirect to */
    public $path;

    /**
     * Redirect constructor.
     *
     * @since 1.0.0
     *
     * @param string|null $path
     */
    public function __construct($path = null) {
        if ($path) {
            $this->setPath($path);
        }
    }

    /**
     * Builds a valid url string with parameters.
     *
     * @since 1.0.0
     *
     * @return string
     * @throws Exception
     */
    private function buildUrlString() : string
    {
        if (! $this->path) {
            throw new Exception('A valid url was not given for the requested redirect');
        }

        $queryString = ! empty($this->queryParameters) ? '?' . ArrayHelper::query($this->queryParameters) : '';

        return "{$this->path}{$queryString}";
    }

    /**
     * Executes the redirect.
     *
     * @TODO: May need to support external redirects differently here.
     *
     * @since 1.0.0
     *
     * @throws Exception
     */
    public function execute()
    {
        wp_safe_redirect($this->buildUrlString());

        exit;
    }

    /**
     * Sets the redirect path.
     *
     * @since 1.0.0
     *
     * @param string $path
     * @return Redirect
     */
    public function setPath(string $path) : Redirect
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Sets the query parameters.
     *
     * @since 1.0.0
     *
     * @param array $params
     * @return Redirect
     */
    public function setQueryParameters(array $params) : Redirect
    {
        $this->queryParameters = $params;

        return $this;
    }
}
