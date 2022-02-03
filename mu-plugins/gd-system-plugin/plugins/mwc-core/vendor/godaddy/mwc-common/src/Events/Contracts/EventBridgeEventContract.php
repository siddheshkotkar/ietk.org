<?php

namespace GoDaddy\WordPress\MWC\Common\Events\Contracts;

use GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;

/**
 * Event bridge contract.
 *
 * @see IsEventBridgeEventTrait when implementing some of the interface methods below
 */
interface EventBridgeEventContract extends EventContract
{
    /**
     * Gets the name of the resource for the current event.
     *
     * @return string
     */
    public function getResource() : string;

    /**
     * Gets the name of the action for the current event.
     *
     * @return string
     */
    public function getAction() : string;

    /**
     * Gets the data for the current event.
     *
     * @return array
     */
    public function getData() : array;

    /**
     * Sets the data for the current event.
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data) : EventBridgeEventContract;
}
