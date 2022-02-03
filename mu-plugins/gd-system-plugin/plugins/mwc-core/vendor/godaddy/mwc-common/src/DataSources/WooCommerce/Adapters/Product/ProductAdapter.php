<?php

namespace GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\Product;

use Exception;
use GoDaddy\WordPress\MWC\Common\DataSources\Contracts\DataSourceAdapterContract;
use GoDaddy\WordPress\MWC\Common\DataSources\WooCommerce\Adapters\CurrencyAmountAdapter;
use GoDaddy\WordPress\MWC\Common\Models\CurrencyAmount;
use GoDaddy\WordPress\MWC\Common\Models\Products\Product;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerceRepository;
use WC_Product;
use WC_Product_External;
use WC_Product_Grouped;
use WC_Product_Variable;
use WC_Product_Variation;

/**
 * Product adapter.
 *
 * Converts between a native product object and a WooCommerce product object.
 */
class ProductAdapter implements DataSourceAdapterContract
{
    /** @var WC_Product WooCommerce product object */
    protected $source;

    /** @var string the product class name */
    protected $productClass = Product::class;

    /**
     * Product adapter constructor.
     *
     * @param WC_Product $product WooCommerce product object.
     */
    public function __construct(WC_Product $product)
    {
        $this->source = $product;
    }

    /**
     * Converts a WooCommerce product object into a native product object.
     *
     * @return Product
     * @throws Exception
     */
    public function convertFromSource() : Product
    {
        return (new $this->productClass())
            ->setId($this->source->get_id())
            ->setName($this->source->get_name())
            ->setRegularPrice($this->convertCurrencyAmountFromSource((float) $this->source->get_regular_price()))
            ->setSalePrice($this->convertCurrencyAmountFromSource((float) $this->source->get_sale_price()))
            ->setType($this->source->get_type())
            ->setStatus($this->source->get_status());
    }

    /**
     * Converts a native product object into a WooCommerce product object.
     *
     * @param Product|null $product native product object to convert
     * @return WC_Product WooCommerce product object
     * @throws Exception
     */
    public function convertToSource($product = null) : WC_Product
    {
        if (! $product instanceof Product) {
            return $this->source;
        }

        $this->instantiateSourceProduct($product);

        $this->source->set_id($product->getId());
        $this->source->set_name($product->getName());
        $this->source->set_regular_price($this->convertCurrencyAmountToSource($product->getRegularPrice()));
        $this->source->set_sale_price($this->convertCurrencyAmountToSource($product->getSalePrice()));
        $this->source->set_status($product->getStatus());

        return $this->source;
    }

    /**
     * Instantiates the proper product according to its type.
     *
     * @param Product|null $product native product object
     * @throws Exception
     */
    protected function instantiateSourceProduct($product = null)
    {
        switch ($product ? $product->getType() : '') {
            case 'external':
                $this->source = new WC_Product_External();
                break;

            case 'grouped':
                $this->source = new WC_Product_Grouped();
                break;

            case 'variable':
                $this->source = new WC_Product_Variable();
                break;

            case 'variation':
                $this->source = new WC_Product_Variation();
                break;

            case 'simple':
            default:
                $this->source = new WC_Product();
        }
    }

    /**
     * Converts a product amount from source.
     *
     * @param float $amount
     * @return CurrencyAmount
     */
    protected function convertCurrencyAmountFromSource(float $amount) : CurrencyAmount
    {
        return (new CurrencyAmountAdapter($amount, WooCommerceRepository::getCurrency()))->convertFromSource();
    }

    /**
     * Converts a product amount to source.
     *
     * @param CurrencyAmount|null $amount
     * @return string
     */
    protected function convertCurrencyAmountToSource($amount) : string
    {
        if (! isset($amount) || 0 === $amount->getAmount()) {
            return '';
        }

        return (new CurrencyAmountAdapter($amount->getAmount(), WooCommerceRepository::getCurrency()))->convertToSource();
    }
}
