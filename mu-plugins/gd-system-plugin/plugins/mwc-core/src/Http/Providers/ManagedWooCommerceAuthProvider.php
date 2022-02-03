<?php

namespace GoDaddy\WordPress\MWC\Core\Http\Providers;

use GoDaddy\WordPress\MWC\Core\Auth\Models\ManagedWooCommerceToken;

/**
 * Provider for MWC authentication tokens.
 */
class ManagedWooCommerceAuthProvider
{
    /**
     * Attempts to retrieve cached token. Otherwise, will request a new token.
     *
     * @return ManagedWooCommerceToken
     */
    public function get() : ManagedWooCommerceToken
    {
        // TODO: implement this method. MWC-3958
        return new ManagedWooCommerceToken();
    }

    /**
     * Clears the cached token.
     *
     * @return ManagedWooCommerceAuthProvider self
     */
    public function forget() : ManagedWooCommerceAuthProvider
    {
        // TODO: implement this method. MWC-3959
        return $this;
    }
}
