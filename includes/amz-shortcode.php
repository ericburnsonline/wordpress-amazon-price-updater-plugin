<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Shortcode registration
|--------------------------------------------------------------------------
|
| Usage:
|
|     [amz_price asin="B00X4WHP5E"]
|
*/

add_action('init', 'amzpu_register_shortcodes');

function amzpu_register_shortcodes()
{
    add_shortcode('amz_price', 'amzpu_shortcode_price');
}

/*
|--------------------------------------------------------------------------
| Shortcode handler
|--------------------------------------------------------------------------
*/

function amzpu_shortcode_price($atts)
{
    $atts = shortcode_atts(
        [
            'asin' => '',
        ],
        $atts,
        'amz_price'
    );

    $asin = strtoupper(trim((string) $atts['asin']));

    if ($asin === '') {
        return '<span class="amzpu-message">' .
            esc_html__('Amazon link unavailable: missing ASIN.', 'amz-price-updater') .
            '</span>';
    }

    if (!preg_match('/^[A-Z0-9]{10}$/', $asin)) {
        return '<span class="amzpu-message">' .
            esc_html__('Amazon link unavailable: invalid ASIN.', 'amz-price-updater') .
            '</span>';
    }

    $partner_tag = amzpu_get_partner_tag();

    if ($partner_tag === '') {
        return '<span class="amzpu-message">' .
            esc_html__(
                'Amazon link unavailable: Associate tag has not been configured.',
                'amz-price-updater'
            ) .
            '</span>';
    }

    $url = add_query_arg(
        [
            'tag' => $partner_tag,
        ],
        'https://www.amazon.com/dp/' . rawurlencode($asin) . '/'
    );

    $display_mode = amzpu_get_display_mode();
    $link_text = amzpu_get_link_text();

    $output = '<span class="amzpu-product-link">';

    if ($display_mode === 'prices_coming_soon') {
        $output .= '<span class="amzpu-status">' .
            esc_html__('Prices coming soon.', 'amz-price-updater') .
            '</span> ';
    } elseif ($display_mode === 'live_prices') {
        /*
         * Live price retrieval is intentionally not enabled in this phase.
         * This prevents accidental display of an unverified or stale price.
         */
        $output .= '<span class="amzpu-status">' .
            esc_html__('Live prices are not enabled yet.', 'amz-price-updater') .
            '</span> ';
    }

    $output .= '<a href="' . esc_url($url) . '" rel="nofollow sponsored">';
    $output .= esc_html($link_text);
    $output .= '</a>';

    $output .= '</span>';

    return apply_filters('amzpu_shortcode_output', $output, $asin, $display_mode);
}
