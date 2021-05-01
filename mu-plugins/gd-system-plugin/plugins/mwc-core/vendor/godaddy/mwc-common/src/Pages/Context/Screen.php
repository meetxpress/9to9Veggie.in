<?php

namespace GoDaddy\WordPress\MWC\Common\Pages\Context;

use GoDaddy\WordPress\MWC\Common\Traits\CanBulkAssignPropertiesTrait;

/**
 * Screen class.
 *
 * Holds information about the current page useful to generate PageViewEvents and to pass page context to the frontend on page load.
 *
 * @since x.y.z
 */
class Screen
{
    use CanBulkAssignPropertiesTrait;

    /**
     * The ID of the page.
     *
     * @var string|null
     */
    protected $pageId;

    /**
     * A list of contexts that apply for the page.
     *
     * @var array
     */
    protected $pageContexts = [];

    /**
     * The ID of the object being displayed on the page, if any.
     *
     * @var string|null
     */
    protected $objectId;

    /**
     * The type of the object being displayed on the page, if any.
     *
     * @var string|null
     */
    protected $objectType;

    /**
     * The status of the object being displayed on the page, if any
     *
     * @var string|null
     */
    protected $objectStatus;

    /**
     * Screen constructor.
     *
     * @since x.y.z
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->setProperties($data);
    }

    /**
     * Gets the ID of the page.
     *
     * @since x.y.z
     *
     * @return string|null
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Gets the list of contexts for the page.
     *
     * @since x.y.z
     *
     * @return array
     */
    public function getPageContexts() : array
    {
        return $this->pageContexts;
    }

    /**
     * Gets the ID of the object.
     *
     * @since x.y.z
     *
     * @return string|null
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Gets the type of the object.
     *
     * @since x.y.z
     *
     * @return string|null
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Gets the status of the object.
     *
     * @since x.y.z
     *
     * @return string|null
     */
    public function getObjectStatus()
    {
        return $this->objectStatus;
    }

    /**
     * Sets the ID of the page.
     *
     * @since x.y.z
     *
     * @param string $pageId
     *
     * @return Screen
     */
    public function setPageId(string $pageId) : Screen
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * Sets the list of contexts for the page.
     *
     * @since x.y.z
     *
     * @param array $pageContexts
     *
     * @return Screen
     */
    public function setPageContexts(array $pageContexts) : Screen
    {
        $this->pageContexts = $pageContexts;

        return $this;
    }

    /**
     * Sets the ID of the object.
     *
     * @since x.y.z
     *
     * @param string $objectId
     *
     * @return Screen
     */
    public function setObjectId(string $objectId) : Screen
    {
        $this->objectId = $objectId;

        return $this;
    }

    /**
     * Sets the type of the object.
     *
     * @since x.y.z
     *
     * @param string $objectType
     *
     * @return Screen
     */
    public function setObjectType(string $objectType) : Screen
    {
        $this->objectType = $objectType;

        return $this;
    }

    /**
     * Sets the status of the object.
     *
     * @since x.y.z
     *
     * @param string $objectStatus
     *
     * @return Screen
     */
    public function setObjectStatus(string $objectStatus) : Screen
    {
        $this->objectStatus = $objectStatus;

        return $this;
    }

    /**
     * Converts Screen data to Array format.
     *
     * @since x.y.z
     *
     * @return array
     */
    public function toArray() : array
    {
        return array_filter([
            'page'   => array_filter([
                'id'       => $this->getPageId(),
                'contexts' => $this->getPageContexts(),
            ]),
            'object' => array_filter([
                'id'     => $this->getObjectId(),
                'type'   => $this->getObjectType(),
                'status' => $this->getObjectStatus(),
            ]),
        ]);
    }
}
