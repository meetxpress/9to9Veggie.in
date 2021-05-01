<?php

namespace GoDaddy\WordPress\MWC\Dashboard\Message;

use DateTime;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;
use GoDaddy\WordPress\MWC\Common\Traits\CanBulkAssignPropertiesTrait;
use GoDaddy\WordPress\MWC\Common\Traits\CanConvertToArrayTrait;

/**
 * MessagesController class.
 *
 * @since x.y.z
 */
class Message
{
    use CanBulkAssignPropertiesTrait;
    use CanConvertToArrayTrait;

    /**
     * Message ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Message subject.
     *
     * @var string
     */
    protected $subject;

    /**
     * Message body.
     *
     * @var string
     */
    protected $body;

    /**
     * Message published date.
     *
     * @var DateTime
     */
    protected $publishedAt;

    /**
     * Message expiration date.
     *
     * @var DateTime
     */
    protected $expiredAt;

    /**
     * Message actions.
     *
     * @var array
     */
    protected $actions = [];

    /**
     * Message rules.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Message links.
     *
     * @var array
     */
    protected $links = [];

    /**
     * Message contexts.
     *
     * @var array
     */
    protected $contexts = [];

    /**
     * Message status context.
     *
     * @var string
     */
    protected $contextStatus;

    /**
     * Message constructor.
     *
     * @param array $messageData
     */
    public function __construct(array $messageData)
    {
        $this->setProperties($messageData);
    }

    /**
     * Checks if message is expired or not.
     *
     * @since x.y.z
     *
     * @return bool
     */
    public function isExpired() : bool
    {
        // get defined expiration date
        $expiredAt = $this->getExpiredAt();

        // calculate expiration date based on the publish date
        if ( empty( $expiredAt ) && ! empty( $publishedAt = $this->getPublishedAt() ) ) {
            try {
                $expiredAt = (clone $publishedAt)->add( new \DateInterval( 'P30D' ) );
            } catch ( \Exception $e ) {}
        }

        // bail if expired at datetime is not set, so we assume it's not expired
        if (! $expiredAt) {
            return false;
        }

        // evaluate expiration date
        return new DateTime() > $expiredAt;
    }

    /**
     * Sets message ID.
     *
     * @param string $id
     *
     * @since x.y.z
     *
     * @return self
     */
    public function setId(string $id) : self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Sets message subject.
     *
     * @param string $subject
     *
     * @since x.y.z
     *
     * @return self
     */
    public function setSubject(string $subject) : self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Sets message body.
     *
     * @param string $body
     *
     * @since x.y.z
     *
     * @return self
     */
    public function setBody(string $body) : self
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Sets message published at.
     *
     * @param DateTime $publishedAt
     *
     * @since x.y.z
     *
     * @return self
     */
    public function setPublishedAt(DateTime $publishedAt) : self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Sets message expired at.
     *
     * @param DateTime|null $expiredAt
     *
     * @since x.y.z
     *
     * @return self
     */
    public function setExpiredAt($expiredAt) : self
    {
        if ($expiredAt) {
            $this->expiredAt = $expiredAt;
        }

        return $this;
    }

    /**
     * Sets message actions.
     *
     * @param array $actions
     *
     * @since x.y.z
     *
     * @return self
     */
    public function setActions(array $actions) : self
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Sets message rules.
     *
     * @param array $rules
     *
     * @since x.y.z
     *
     * @return self
     */
    public function setRules(array $rules) : self
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Sets message links.
     *
     * @param array $links
     *
     * @since x.y.z
     *
     * @return self
     */
    public function setLinks(array $links) : self
    {
        $this->links = $links;

        return $this;
    }

    /**
     * Sets the message contexts.
     *
     * @param array $contexts the message contexts
     *
     * @since x.y.z
     *
     * @return self
     */
    public function setContexts(array $contexts) : self
    {
        $this->contexts = $contexts;

        return $this;
    }

    /**
     * Sets the message context status.
     *
     * @param string $contextStatus the message context status
     *
     * @since x.y.z
     *
     * @return self
     */
    public function setContextStatus(string $contextStatus) : self
    {
        $this->contextStatus = $contextStatus;

        return $this;
    }

    /**
     * Gets message ID.
     *
     * @since x.y.z
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets message subject.
     *
     * @since x.y.z
     *
     * @return string|null
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Gets message body.
     *
     * @since x.y.z
     *
     * @return string|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Gets message published datetime object.
     *
     * @since x.y.z
     *
     * @return DateTime|null
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Gets message expired datetime object.
     *
     * @since x.y.z
     *
     * @return null|DateTime
     */
    public function getExpiredAt()
    {
        return $this->expiredAt;
    }

    /**
     * Gets message actions.
     *
     * @since x.y.z
     *
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * Gets message rules.
     *
     * @since x.y.z
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Gets message links.
     *
     * @since x.y.z
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Gets the message contexts.
     *
     * @since x.y.z
     *
     * @return array
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * Gets the message context status.
     *
     * @since x.y.z
     *
     * @return string
     */
    public function getContextStatus() : string
    {
        return $this->contextStatus;
    }

    /**
     * Gets the associated message status.
     *
     * @since x.y.z
     *
     * @param null|int|string $userId
     *
     * @return MessageStatus
     */
    public function status($userId = null) : MessageStatus
    {
        if (! is_numeric($userId) || ! $userId) {
            $userId = WordPressRepository::getUser()->ID;
        }

        return new MessageStatus($this, (int) $userId);
    }
}
