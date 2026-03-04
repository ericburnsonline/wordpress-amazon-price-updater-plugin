<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Shortcode registration
|--------------------------------------------------------------------------
*/

add_shortcode('amz_price', 'amzpu_shortcode_price');


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
        $atts
    );

    $asin = strtoupper(trim($atts['asin']));

    if (!$asin) {
        return '';
    }

    $cache_key = 'amzpu_' . $asin;
    $cached = get_transient($cache_key);

    if ($cached && isset($cached['price'])) {
        $date = date('n/j/Y', $cached['checked_at']);
        return esc_html($cached['price'] . ' as of ' . $date);
    }

    /*
    If price not cached, schedule refresh and return placeholder
    */

    if (!wp_next_scheduled('amzpu_refresh_asin_event', [$asin])) {
        wp_schedule_single_event(time() + 60, 'amzpu_refresh_asin_event', [$asin]);
    }

    return 'Price updating…';
}


/*
|--------------------------------------------------------------------------
| Background refresh hook
|--------------------------------------------------------------------------
*/

add_action('amzpu_refresh_asin_event', 'amzpu_refresh_asin');


/*
|--------------------------------------------------------------------------
| Refresh ASIN price (placeholder implementation)
|--------------------------------------------------------------------------
*/

function amzpu_refresh_asin($asin)
{
    $asin = strtoupper(trim($asin));

    if (!preg_match('/^[A-Z0-9]{10}$/', $asin)) {
        return;
    }

    /*
    Placeholder implementation.

    In a future version this function will call the Amazon Product Advertising API
    and retrieve the Buy Box price.

    For now we simply store a dummy value so the shortcode flow works.
    */

    $price = '$19.95';

    set_transient(
        'amzpu_' . $asin,
        [
            'price' => $price,
            'checked_at' => time(),
        ],
        DAY_IN_SECONDS
    );
}
