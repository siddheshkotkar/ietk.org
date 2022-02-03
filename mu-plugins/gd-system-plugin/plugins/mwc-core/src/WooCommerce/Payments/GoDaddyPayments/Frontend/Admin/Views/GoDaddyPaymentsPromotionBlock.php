<?php

namespace GoDaddy\WordPress\MWC\Core\WooCommerce\Payments\GoDaddyPayments\Frontend\Admin\Views;

use GoDaddy\WordPress\MWC\Common\Helpers\ArrayHelper;
use GoDaddy\WordPress\MWC\Common\Repositories\WordPressRepository;

class GoDaddyPaymentsPromotionBlock
{
    /** @var string source parameter for the URL of the primary button */
    protected $source;

    public function __construct(string $source)
    {
        $this->source = $source;
    }

    /**
     * Gets the title of the promotion block.
     *
     * @since 2.13.0
     *
     * @return string
     */
    public function getTitle() : string
    {
        return __('Get paid fast with GoDaddy Payments', 'mwc-core');
    }

    /**
     * Gets the description of the promotion block.
     *
     * @since 2.13.0
     *
     * @return string
     */
    public function getDescription() : string
    {
        return __('Securely accept credit and debit card payments in minutes and get next-day payouts. Free setup and no hidden fees or long-term contracts.', 'mwc-core');
    }

    /**
     * Gets HTML for the description of the promotion block.
     *
     * @since 2.13.0
     *
     * @return string
     */
    public function getDescriptionHtml() : string
    {
        return '<div id="godaddy-payments-promotion-block-placeholder">'.wp_kses_post($this->getDescription()).'</div>';
    }

    /**
     * Gets the label for the primary button of the promotion block.
     *
     * @since 2.13.0
     *
     * @return string
     */
    public function getPrimaryButtonLabel() : string
    {
        return _x('Set up', 'GoDaddy Payments promotion block button', 'mwc-core');
    }

    /**
     * Gets the URL for the primary button of the promotion block.
     *
     * @since 2.13.0
     *
     * @return string
     */
    public function getPrimaryButtonUrl() : string
    {
        $params = [
            'page'     => 'wc-settings',
            'tab'      => 'checkout',
            'gdpsetup' => 'true',
            'source'   => $this->source,
        ];

        return admin_url('admin.php?'.ArrayHelper::query(array_filter($params)));
    }

    /**
     * Gets the definition of the promotion block in the format used by the WC_Admin_Addons class.
     *
     * @since 2.13.0
     *
     * @return object
     */
    public function getDefinition()
    {
        return (object) [
            'module'        => 'promotion_block',
            'title'         => $this->getTitle(),
            'image'         => '',
            'image_alt'     => $this->getTitle(),
            'description'   => $this->getDescriptionHtml(),
            'button_1'      => $this->getPrimaryButtonLabel(),
            'button_1_href' => $this->getPrimaryButtonUrl(),
            'button_2'      => false,
            'plugin'        => null,
        ];
    }

    /**
     * Renders a promotion block for GoDaddy Payments to replace the promotion block generated by WC_Admin_Addons.
     *
     * @since 2.13.0
     *
     * @return string
     */
    public function render() : string
    {
        $html = '<div id="godaddy-payments-promotion-block">';

        $html .= '<img id="godaddy-payments-logo" src="'.esc_url(WordPressRepository::getAssetsUrl('images/branding/gd-logo.svg')).'" />';
        $html .= '<h1 id="godaddy-payments-title">'.esc_attr($this->getTitle()).'</h1>';
        $html .= '<div id="godaddy-payments-description-wrapper"><p id="godaddy-payments-description">'.esc_attr($this->getDescription()).'</p></div>';
        $html .= '<div id="godaddy-payments-button-wrapper"><a href="'.esc_attr($this->getPrimaryButtonUrl()).'" id="godaddy-payments-button">'.esc_attr($this->getPrimaryButtonLabel()).'</a></div>';
        $html .= '</div>';

        return $html;
    }
}