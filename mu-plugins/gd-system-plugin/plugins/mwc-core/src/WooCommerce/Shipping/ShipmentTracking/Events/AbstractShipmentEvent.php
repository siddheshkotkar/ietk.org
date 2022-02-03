<?php

namespace GoDaddy\WordPress\MWC\Core\WooCommerce\Shipping\ShipmentTracking\Events;

use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventBridgeEventContract;
use GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;
use GoDaddy\WordPress\MWC\Shipping\Events\AbstractShipmentEvent as ShippingAbstractShipmentEvent;

/**
 * Shipment event abstract class.
 */
abstract class AbstractShipmentEvent extends ShippingAbstractShipmentEvent implements EventBridgeEventContract
{
    use IsEventBridgeEventTrait;

    /**
     * Builds the initial data for the current event.
     *
     * @return array
     */
    protected function buildInitialData() : array
    {
        $providerName = $this->getShipment()->getProviderName();
        $packages = $this->getShipment()->getPackages();

        return [
            'order' => [
                'id' => $this->getOrderFulfillment()->getOrder()->getId(),
            ],
            'shipment' => [
                'id' => $this->getShipment()->getId(),
                'isKnownProvider' => 'other' !== $providerName,
                'providerName' => $providerName,
                'providerLabel' => $this->getShipment()->getProviderLabel(),
                'trackingUrl' => ! empty($packages) ? current($packages)->getTrackingUrl() : '',
                'itemsCount' => ! empty($packages) ? count(current($packages)->getItems()) : 0,
            ],
        ];
    }
}
