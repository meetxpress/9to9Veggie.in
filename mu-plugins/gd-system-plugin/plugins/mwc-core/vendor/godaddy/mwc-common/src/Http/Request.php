<?php

namespace GoDaddy\WordPress\MWC\Common\Http;

use Exception;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\ManagedWooCommerceRepository;

/**
 * Request handler.
 *
 * @since 1.0.0
 */
class Request
{
    /** @var array request body */
    public $body;

    /** @var array request headers */
    public $headers;

    /** @var string request method */
    public $method;

    /** @var object|array request query parameters */
    public $query;

    /** @var bool whether should verify SSL */
    public $sslVerify;

    /** @var int default timeout in seconds */
    public $timeout;

    /** @var string the URL to send the request to */
    public $url;

    /**
     * Request constructor.
     *
     * @since 1.0.0
     *
     * @param string|null $url
     * @throws Exception
     */
    public function __construct(string $url = null)
    {
        $this->headers()
            ->setMethod()
            ->sslVerify()
            ->timeout();

        if ($url) {
            $this->url($url);
        }
    }

    /**
     * Sets the body of the request.
     *
     * @since 1.0.0
     *
     * @param array $body
     * @return Request
     */
    public function body(array $body) : Request
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Builds a valid url string with parameters.
     *
     * @since 1.0.0
     *
     * @return string
     * @throws Exception
     */
    protected function buildUrlString() : string
    {
        $queryString = ! empty($this->query) ? '?'.ArrayHelper::query($this->query) : '';

        return $this->url.$queryString;
    }

    /**
     * Sets Request headers.
     *
     * @since 1.0.0
     *
     * @param array|null $additionalHeaders
     * @return Request
     * @throws Exception
     */
    public function headers($additionalHeaders = []) : Request
    {
        $this->headers = ArrayHelper::combine(['Content-Type' => 'application/json'], $additionalHeaders);

        return $this;
    }

    /**
     * Sets the request method.
     *
     * @since x.y.z
     *
     * @param string $method
     * @return Request
     */
    public function setMethod(string $method = 'get') : Request
    {
        if (! ArrayHelper::contains(['GET', 'POST', 'HEAD', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'PATCH'], strtoupper($method))) {
            $method = 'get';
        }

        $this->method = strtoupper($method);

        return $this;
    }

    /**
     * Sets query parameters.
     *
     * @since 1.0.0
     *
     * @param array $params
     * @return Request
     */
    public function query(array $params) : Request
    {
        $this->query = $params;

        return $this;
    }

    /**
     * Sends the request.
     *
     * @since 1.0.0
     *
     * @return Response
     * @throws Exception
     */
    public function send() : Response
    {
        $this->validate();

        return new Response(wp_remote_request($this->buildUrlString(), [
            'body'      => $this->body ? json_encode($this->body) : null,
            'headers'   => $this->headers,
            'method'    => $this->method,
            'sslverify' => $this->sslVerify,
            'timeout'   => $this->timeout,
        ]));
    }

    /**
     * Sets SSL verify.
     *
     * @since 1.0.0
     *
     * @param bool $default
     * @return Request
     */
    public function sslVerify($default = false) : Request
    {
        $this->sslVerify = $default || ManagedWooCommerceRepository::isProductionEnvironment();

        return $this;
    }

    /**
     * Sets the request timeout.
     *
     * @since 1.0.0
     *
     * @param int $seconds
     * @return Request
     */
    public function timeout(int $seconds = 30) : Request
    {
        $this->timeout = $seconds;

        return $this;
    }

    /**
     * Sets the url of the request.
     *
     * @since 1.0.0
     *
     * @param string $url
     * @return Request
     */
    public function url(string $url) : Request
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Validates the request.
     *
     * @since 1.0.0
     *
     * @throws Exception
     */
    protected function validate()
    {
        if (! $this->url) {
            throw new Exception('You must provide a url for an outgoing request');
        }
    }
}
