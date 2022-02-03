<?php

namespace GoDaddy\WordPress\MWC\Core\Payments\Events\Transformers;

use Exception;
use GoDaddy\WordPress\MWC\Common\Events\AbstractEventTransformer;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventContract;
use GoDaddy\WordPress\MWC\Common\Events\ModelEvent;
use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerceRepository;

/**
 * Transformer to add data to order events.
 */
class OrderEventTransformer extends AbstractEventTransformer
{
    /**
     * Determines whether the event must be transformed or not.
     *
     * @param ModelEvent|EventContract $event
     * @return bool
     */
    public function shouldHandle(EventContract $event): bool
    {
        return $event instanceof ModelEvent && 'order' === $event->getResource();
    }

    /**
     * Handles and perhaps modifies the event.
     *
     * @param ModelEvent|EventContract $event the event, perhaps modified by the method
     * @throws Exception
     */
    public function handle(EventContract $event)
    {
        $data = $event->getData();

        ArrayHelper::set($data, 'resource.currency', WooCommerceRepository::getCurrency());

        $event->setData($data);
    }
}
