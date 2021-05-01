<?php

namespace GoDaddy\WordPress\MWC\Common\Model\Contracts;

/**
 * Model contract.
 *
 * @since x.y.z
 */
interface ModelContract
{
    /**
     * Creates a new instance of the given model class and save it.
     *
     * @since x.y.z
     */
    public static function create();

    /**
     * Updates a given instance of the model class.
     *
     * @since x.y.z
     */
    public function update();

    /**
     * Updates a given instance of the model class.
     *
     * @since x.y.z
     */
    public function delete();

    /**
     * Saves the instance of the class with its current state.
     *
     * @since x.y.z
     */
    public function save();

    /**
     * Seeds an instance of the given model class without saving it.
     *
     * @since x.y.z
     */
    public static function seed();
}
