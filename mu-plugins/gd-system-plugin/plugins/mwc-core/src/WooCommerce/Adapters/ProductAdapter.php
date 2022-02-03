<?php

namespace GoDaddy\WordPress\MWC\Core\WooCommerce\Adapters;

use GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\Product\ProductAdapter as CommonProductAdapter;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Models\Products\Product;

/**
 * Core product adapter.
 *
 * Converts between a native core product object and a WooCommerce product object.
 */
class ProductAdapter extends CommonProductAdapter
{
    /** @var string the product class name */
    protected $productClass = Product::class;
}
