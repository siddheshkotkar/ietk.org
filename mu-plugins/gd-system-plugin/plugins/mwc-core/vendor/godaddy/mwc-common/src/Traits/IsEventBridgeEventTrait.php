<?php

namespace GoDaddy\WordPress\MWC\Common\Traits;

use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventBridgeEventContract;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;

/**
 * Trait for event bridges.
 *
 * @see EventBridgeEventContract interface - this trait implements some of its methods
 */
trait IsEventBridgeEventTrait
{
    /** @var string the name of the event resource */
    protected $resource;

    /** @var string the name of the event action */
    protected $action;

    /** @var array the data for this event */
    protected $data;

    /**
     * Gets the name of the resource for the event.
     *
     * @return string
     */
    public function getResource() : string
    {
        return $this->resource ?: '';
    }

    /**
     * Gets the name of the action for the event.
     *
     * @return string
     */
    public function getAction() : string
    {
        return $this->action ?: '';
    }

    /**
     * Sets the data for the event.
     *
     * @param array $data
     * @return self|EventBridgeEventContract
     */
    public function setData(array $data) : EventBridgeEventContract
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Gets the data for the event, initializing it if needed.
     *
     * @return array
     */
    public function getData() : array
    {
        if (null === $this->data) {
            $this->data = $this->buildInitialData();
        }

        return ArrayHelper::wrap($this->data);
    }

    /**
     * Returns an array with initial data for this event.
     *
     * Subclasses can override this method to initialize the data based on the objects associated with the particular event.
     *
     * @return array
     */
    protected function buildInitialData() : array
    {
        return [];
    }
}
