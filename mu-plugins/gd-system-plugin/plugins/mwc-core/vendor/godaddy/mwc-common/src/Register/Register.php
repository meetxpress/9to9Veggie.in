<?php

namespace GoDaddy\WordPress\MWC\Common\Register;

use Closure;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Register\Types\RegisterAction;
use GoDaddy\WordPress\MWC\Common\Register\Types\RegisterFilter;

/**
 * Registers an item.
 *
 * @since 1.0.0
 */
class Register
{
    /** @var string registration type */
    protected $registrableType;

    /** @var string group name containing other items the item should be registered with */
    protected $groupName;

    /** @var array|string|Closure function, method or closure to be attached to the registered item's execution */
    protected $handler;

    /** @var int number of arguments to pass the handler */
    protected $numberOfArguments;

    /** @var int priority of the item being registered */
    protected $processPriority;

    /** @var callable condition for successful registration */
    protected $registrableCondition;

    /**
     * Registers an action.
     *
     * @since 1.0.0
     *
     * @return RegisterAction
     */
    public static function action() : RegisterAction
    {
        return new RegisterAction();
    }

    /**
     * Registers a filter.
     *
     * @since 1.0.0
     *
     * @return RegisterFilter
     */
    public static function filter() : RegisterFilter
    {
        return new RegisterFilter();
    }

    /**
     * Sets the registrable type for the current object.
     *
     * @since 1.0.0
     *
     * @param string $type a registrable type
     * @return self
     */
    protected function setType(string $type) : self
    {
        $this->registrableType = $type;

        return $this;
    }

    /**
     * Gets the registrable type.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function getType() : string
    {
        return $this->registrableType ?: '';
    }

    /**
     * Sets the group name to register the handler to.
     *
     * @since 1.0.0
     *
     * @param string $name name of the group to register the handler to
     * @return self
     */
    public function setGroup(string $name) : self
    {
        $this->groupName = $name;

        return $this;
    }

    /**
     * Sets a handler for the item to register.
     *
     * @since 1.0.0
     *
     * @param string|array|Closure $handler function name (string), static method name (string) or array (object name, method name)
     * @return self
     */
    public function setHandler($handler) : self
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Determines if the item to register has a handler attached.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    protected function hasHandler() : bool
    {
        return null !== $this->handler && ($this->handler instanceof Closure || is_callable($this->handler));
    }

    /**
     * Sets the priority for where in the overall order the registration should be processed.
     *
     * @since 1.0.0
     *
     * @param int|null $priority
     * @return self
     */
    public function setPriority(int $priority = null) : self
    {
        $this->processPriority = $priority;

        return $this;
    }

    /**
     * Sets if the arguments to pass to the handler upon registration.
     *
     * @since 1.0.0
     *
     * @param int $arguments
     * @return self
     */
    public function setArgumentsCount(int $arguments) : self
    {
        $this->numberOfArguments = $arguments;

        return $this;
    }

    /**
     * Sets a condition according to which the registration should apply.
     *
     * @param callable $registrableCondition
     * @return self
     *@since 1.0.0
     */
    public function setCondition(callable $registrableCondition) : self
    {
        $this->registrableCondition = $registrableCondition;

        return $this;
    }

    /**
     * Removes a condition for registration to apply (will always apply).
     *
     * @since 1.0.0
     *
     * @return self
     */
    public function removeCondition() : self
    {
        $this->registrableCondition = null;

        return $this;
    }

    /**
     * Determines whether there is a condition to apply the registration.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    protected function hasCondition() : bool
    {
        return null !== $this->registrableCondition;
    }

    /**
     * Determines whether the registration should apply based on the defined condition, if present.
     *
     * @since 1.0.0
     *
     * @return bool
     */
    protected function shouldRegister() : bool
    {
        return ! $this->hasCondition() || call_user_func($this->registrableCondition);
    }
}
