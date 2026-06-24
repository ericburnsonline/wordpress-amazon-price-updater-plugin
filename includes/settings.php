<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Shared plugin settings
|--------------------------------------------------------------------------
*/

function amzpu_get_settings()
{
    $settings = get_option('amzpu_settings', []);

    if (!is_array($settings)) {
        $settings = [];
    }

    return wp_parse_args(
        $settings,
        [
            'partner_tag'  => '',
            'display_mode' => 'prices_coming_soon',
            'link_text'    => 'View on Amazon',
        ]
    );
}

function amzpu_get_partner_tag()
{
    $settings = amzpu_get_settings();

    return trim((string) $settings['partner_tag']);
}

function amzpu_get_display_mode()
{
    $settings = amzpu_get_settings();
    $mode = $settings['display_mode'];

    $allowed_modes = [
        'links_only',
        'prices_coming_soon',
        'live_prices',
    ];

    if (!in_array($mode, $allowed_modes, true)) {
        return 'prices_coming_soon';
    }

    return $mode;
}

function amzpu_get_link_text()
{
    $settings = amzpu_get_settings();
    $text = trim((string) $settings['link_text']);

    return $text !== '' ? $text : 'View on Amazon';
}
