<?php

namespace GoDaddy\WordPress\MWC\Core\Events;

use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventBridgeEventContract;
use GoDaddy\WordPress\MWC\Common\Extensions\Types\PluginExtension;
use GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;

/**
 * Abstract plugin event class.
 */
abstract class AbstractPluginEvent implements EventBridgeEventContract
{
    use IsEventBridgeEventTrait;

    /** @var PluginExtension */
    protected $plugin;

    /**
     * Constructor.
     *
     * @param PluginExtension $plugin
     */
    public function __construct(PluginExtension $plugin)
    {
        $this->resource = 'plugin';
        $this->plugin = $plugin;
    }

    /**
     * Builds the initial data for the current event.
     *
     * @return array
     */
    protected function buildInitialData() : array
    {
        return [
            'plugin' => [
                'slug' => $this->plugin->getSlug(),
            ],
        ];
    }
}
