<?php

namespace GoDaddy\WordPress\MWC\Common\Register\Types;

use Exception;
use GoDaddy\WordPress\MWC\Common\Register\Contracts\RegistrableContract;
use GoDaddy\WordPress\MWC\Common\Register\Register;

/**
 * WordPress filter registration wrapper.
 *
 * @since 1.0.0
 */
final class RegisterFilter extends Register implements RegistrableContract
{
    /**
     * Registrable filter constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->setType('filter');
        $this->setPriority(10);
        $this->setArgumentsCount(1);
    }

    /**
     * Executes the deregistration.
     *
     * @since 1.0.0
     *
     * @throws Exception
     */
    public function deregister()
    {
        $this->validate();

        remove_filter($this->groupName, $this->handler, $this->processPriority);
    }

    /**
     * Executes the registration.
     *
     * @since 1.0.0
     *
     * @throws Exception
     */
    public function execute()
    {
        $this->validate();

        if ($this->shouldRegister()) {
            add_filter($this->groupName, $this->handler, $this->processPriority, $this->numberOfArguments);
        }
    }

    /**
     * Validates the current instance settings.
     *
     * @since 1.0.0
     *
     * @throws Exception
     */
    public function validate()
    {
        if (! $this->groupName) {
            throw new Exception('Cannot register a filter: the group to assign the filter to is not specified.');
        }

        if (! $this->hasHandler()) {
            throw new Exception("Cannot register a filter for `{$this->groupName}`: the provided handler does not exist or is not callable.");
        }
    }
}
