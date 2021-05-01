<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Support;

use Exception;
use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Helpers\StringHelper;
use GoDaddy\WordPress\MWC\Common\Http\GoDaddyRequest;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Dashboard\Exceptions\SupportRequestFailedException;
use GoDaddy\WordPress\MWC\Dashboard\Repositories\UserRepository;
use GoDaddy\WordPress\MWC\Dashboard\Repositories\WooCommercePluginsRepository;

class SupportRequest
{
    /** @var string email the request is from. */
    protected $from;

    /** @var string The message to include with the support request. */
    protected $message;

    /** @var string The reason for the support request. */
    protected $reason;

    /** @var string The subject of the support request. */
    protected $subject;

    /** @var string The extension this request is about. */
    protected $subjectExtension;

    /**
     * Sends a request to the Extensions API to create a support request.
     *
     * @throws Exception|SupportRequestFailedException
     */
    public function send()
    {
        $response = (new GoDaddyRequest)->url(StringHelper::trailingSlash(Configuration::get('mwc.extensions.api.url')) . 'support/request')
            ->headers([
                'X-Account-UID' => Configuration::get('godaddy.account.uid', ''),
                'X-Site-Token'  => Configuration::get('godaddy.site.token', 'empty'),
            ])
            ->body([
                'data' => $this->getFormattedRequestData(),
                'from' => $this->getFrom(),
            ])
            ->setMethod('POST')
            ->send();

        if ($response->isError() || $response->getStatus() !== 200) {
            throw new SupportRequestFailedException("Could not send the support request ({$response->getStatus()}): {$response->getErrorMessage()}");
        }
    }

    /**
     * Gets the formatted data for the request
     *
     * @return array
     * @throws Exception
     */
    protected function getFormattedRequestData() : array
    {
        $requestingUser   = $this->getRequestingUser();
        $subjectExtension = $this->getSubjectExtension();
        $supportUser      = WordPressRepository::getUserByEmail(Configuration::get('support.support_user.email'))
            ?: WordPressRepository::getUserByLogin(Configuration::get('support.support_user.login'));

        $data = [
            'ticket'               => [
                'subject'     => $this->getSubject(),
                'description' => $this->getMessage(),
            ],
            'customer'             => [
                // @TODO: We should have our own User class that returns custom items like this as an extension {JO 2021-03-06}
                'name'  => $requestingUser ? UserRepository::getUserName($requestingUser) : '',
                'email' => $this->getFrom(),
            ],
            'reason'               => $this->getReason(),
            'plugin'               => [
                'name'             => ArrayHelper::get($subjectExtension, 'Name', ''),
                'version'          => ArrayHelper::get($subjectExtension, 'Version', ''),
                'support_end_date' => ! empty($subjectExtension) ? WooCommercePluginsRepository::getWooCommerceSubscriptionEnd($subjectExtension) : '',
            ],
            'support_bot_context' => Support::getConnectType(),
            'system_status_report' => $this->getSystemStatus(),
        ];

        if ($supportUser) {
            ArrayHelper::set($data, 'support_user.user_id', $supportUser->ID);
            ArrayHelper::set($data, 'support_user.password_reset_url', UserRepository::getPasswordResetUrl($supportUser));
        }

        return $data;
    }

    /**
     * Get the email the request is coming from
     *
     * @return string|null
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Get the request message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Get the reason for the request
     *
     * @return string|null
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Get the user making the request
     *
     * @return \WP_User
     */
    public function getRequestingUser()
    {
        if ($this->getFrom()) {
            return WordPressRepository::getUserByEmail($this->getFrom()) ?: WordPressRepository::getUser();
        }

        return WordPressRepository::getUser();
    }

    /**
     * Get the subject of the request
     *
     * @return string|null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Get the extension this request is about
     *
     * @return array
     */
    public function getSubjectExtension() : array
    {
        if ($this->subjectExtension) {
            return WooCommercePluginsRepository::getPluginDataBySlug($this->subjectExtension);
        }

        return [];
    }

    /**
     * Set the from address of the requesting user
     *
     * @param string $email
     *
     * @return SupportRequest
     */
    public function setFrom(string $email) : SupportRequest
    {
        $this->from = $email;

        return $this;
    }

    /**
     * Set the message of the Request
     *
     * @param string $message
     *
     * @return SupportRequest
     */
    public function setMessage(string $message) : SupportRequest
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set the reason for the request
     *
     * @param string $reason
     *
     * @return SupportRequest
     */
    public function setReason(string $reason) : SupportRequest
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Set the subject of the request
     *
     * @param string $subject
     *
     * @return SupportRequest
     */
    public function setSubject(string $subject) : SupportRequest
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the subject extension
     *
     * @param string $extensionSlug
     *
     * @return SupportRequest
     */
    public function setSubjectExtension(string $extensionSlug) : SupportRequest
    {
        $this->subjectExtension = $extensionSlug;

        return $this;
    }

    /**
     * Get the WC system status data
     */
    protected function getSystemStatus()
    {
        return WC()->api->get_endpoint_data('/wc/v3/system_status');
    }
}
