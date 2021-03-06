<?php

namespace GoDaddy\WordPress\MWC\Core\WooCommerce\Payments\Events;

use GoDaddy\WordPress\MWC\Common\Events\Contracts\EventBridgeEventContract;
use GoDaddy\WordPress\MWC\Common\Traits\IsEventBridgeEventTrait;

/**
 * Payment transaction created event.
 */
class PaymentTransactionCreatedEvent implements EventBridgeEventContract
{
    use IsEventBridgeEventTrait;

    /** @var string The name of the payments provider */
    protected $providerName;

    /**
     * Constructor.
     *
     * @param string $providerName
     */
    public function __construct(string $providerName)
    {
        $this->resource = 'payment_transaction';
        $this->action = 'create';
        $this->providerName = $providerName;
    }

    /**
     * Builds the initial data for the event.
     *
     * @return array
     */
    protected function buildInitialData() : array
    {
        return [
            'paymentTransaction' => [
                'providerName' => $this->providerName,
            ],
        ];
    }
}
