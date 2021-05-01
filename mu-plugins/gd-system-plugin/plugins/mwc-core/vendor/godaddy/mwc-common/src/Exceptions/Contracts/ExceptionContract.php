<?php

namespace GoDaddy\WordPress\MWC\Common\Exceptions\Contracts;

/**
 * Exception contract.
 *
 * @since x.y.z
 */
interface ExceptionContract
{
    /**
     * Constructor accepting a message passed in by the specific use case.
     *
     * @since x.y.z
     *
     * @param string $message
     * @return void
     */
    public function __construct(string $message);

    /**
     * Contains the logic and functionality to complete when the Exception has finished processing.
     *
     * @since x.y.z
     *
     * @return mixed|void
     */
    public function callback();

    /**
     * Gets the exception error code.
     *
     * @since x.y.z
     *
     * @return mixed
     */
    public function getCode();

    /**
     * Gets the context included as an array with the Exception.
     *
     * @since x.y.z
     *
     * @return array
     */
    public function getContext() : array;

    /**
     * Gets the file in which the exception occurred.
     *
     * @since x.y.z
     *
     * @return string
     */
    public function getFile();

    /**
     * Gets the error level of the exception.
     *
     * @since x.y.z
     *
     * @return string
     */
    public function getLevel() : string;

    /**
     * Gets the line on which the exception occurred.
     *
     * @since x.y.z
     *
     * @return int
     */
    public function getLine();

    /**
     * Gets the exception error message.
     *
     * @since x.y.z
     *
     * @return string
     */
    public function getMessage();
}
