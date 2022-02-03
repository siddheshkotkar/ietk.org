<?php

namespace GoDaddy\WordPress\MWC\Core\WooCommerce\Models\Products;

use GoDaddy\WordPress\MWC\Common\Events\Events;
use GoDaddy\WordPress\MWC\Common\Events\Exceptions\EventTransformFailedException;
use GoDaddy\WordPress\MWC\Common\Models\Products\Product as CommonProduct;

/**
 * Core product object.
 */
class Product extends CommonProduct
{
    /**
     * Updates the product.
     *
     * This method also broadcast model events.
     *
     * @return self
     * @throws EventTransformFailedException
     */
    public function update() : Product
    {
        $product = parent::update();

        Events::broadcast($this->buildEvent('product', 'update'));

        return $product;
    }

    /**
     * Saves the product.
     *
     * This method also broadcast model events.
     *
     * @return self
     * @throws EventTransformFailedException
     */
    public function save() : Product
    {
        $product = parent::save();

        Events::broadcast($this->buildEvent('product', 'create'));

        return $product;
    }
}
