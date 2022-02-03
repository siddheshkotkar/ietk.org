<?php

namespace GoDaddy\WordPress\MWC\Core\WooCommerce\Interceptors;

use Exception;
use GoDaddy\WordPress\MWC\Common\Models\Products\Product as CommonProduct;
use GoDaddy\WordPress\MWC\Common\Register\Register;
use GoDaddy\WordPress\MWC\Common\Repositories\WooCommerce\ProductsRepository;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Adapters\ProductAdapter;
use GoDaddy\WordPress\MWC\Core\WooCommerce\Interceptors\Contracts\InterceptorContract;
use GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag;
use WC_Product;
use WP_Post;

/**
 * A WooCommerce interceptor to hook on product actions and filters.
 */
class ProductInterceptor implements InterceptorContract
{
    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public function addHooks()
    {
        Register::action()
            ->setGroup('wp_insert_post')
            ->setHandler([$this, 'onWpInsertPost'])
            ->setArgumentsCount(3)
            ->execute();

        Register::action()
            ->setGroup('woocommerce_update_product')
            ->setHandler([$this, 'onWooCommerceUpdateProduct'])
            ->execute();
    }

    /**
     * Turns the new product flag on if the post created was a product.
     *
     * @internal
     *
     * @param int|string $postId
     * @param WP_Post $post
     * @param bool $isUpdate
     */
    public function onWpInsertPost($postId, $post, $isUpdate)
    {
        $this->maybeFlagNewProduct($postId, $post, $isUpdate);
    }

    /**
     * Calls the core product CRUD methods.
     *
     * @internal
     *
     * @param int $postId
     *
     * @throws Exception
     */
    public function onWooCommerceUpdateProduct($postId)
    {
        if (! ($wcProduct = ProductsRepository::get((int) $postId))) {
            return;
        }

        $newProductFlag = $this->getNewProductFlag($wcProduct->get_id());

        $product = $this->getConvertedProduct($wcProduct);

        if ($newProductFlag->isOn()) {
            $product->save();

            $newProductFlag->turnOff();
        } else {
            $product->update();
        }
    }

    /**
     * Turns the new product flag on if the post created was a product.
     *
     * @param int|string $postId
     * @param WP_Post $post
     * @param bool $isUpdate
     */
    protected function maybeFlagNewProduct($postId, $post, $isUpdate)
    {
        if (! $isUpdate && $post->post_type === 'product') {
            $this->getNewProductFlag((int) $postId)->turnOn();
        }
    }

    /**
     * Gets the new product flag instance for the given product id.
     *
     * @param int $productId
     * @return NewWooCommerceObjectFlag
     */
    protected function getNewProductFlag(int $productId) : NewWooCommerceObjectFlag
    {
        return new NewWooCommerceObjectFlag($productId);
    }

    /**
     * Converts a WooCommerce product object into a native product object.
     *
     * @param WC_Product $product
     * @return CommonProduct
     * @throws Exception
     */
    protected function getConvertedProduct(WC_Product $product) : CommonProduct
    {
        return (new ProductAdapter($product))->convertFromSource();
    }
}
