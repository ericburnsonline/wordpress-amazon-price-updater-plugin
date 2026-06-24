<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Admin menu
|--------------------------------------------------------------------------
*/

add_action('admin_menu', 'amzpu_add_settings_page');

function amzpu_add_settings_page()
{
    add_options_page(
        __('AMZ Price Updater', 'amz-price-updater'),
        __('AMZ Price Updater', 'amz-price-updater'),
        'manage_options',
        'amzpu-settings',
        'amzpu_render_settings_page'
    );
}

/*
|--------------------------------------------------------------------------
| Settings registration
|--------------------------------------------------------------------------
*/

add_action('admin_init', 'amzpu_register_settings');

function amzpu_register_settings()
{
    register_setting(
        'amzpu_settings_group',
        'amzpu_settings',
        [
            'type'              => 'array',
            'sanitize_callback' => 'amzpu_sanitize_settings',
            'default'           => [],
        ]
    );

    add_settings_section(
        'amzpu_affiliate_settings',
        __('Amazon Associate Link Settings', 'amz-price-updater'),
        'amzpu_render_settings_section',
        'amzpu-settings'
    );

    add_settings_field(
        'partner_tag',
        __('Amazon Associate Tag', 'amz-price-updater'),
        'amzpu_render_partner_tag_field',
        'amzpu-settings',
        'amzpu_affiliate_settings'
    );

    add_settings_field(
        'display_mode',
        __('Display Mode', 'amz-price-updater'),
        'amzpu_render_display_mode_field',
        'amzpu-settings',
        'amzpu_affiliate_settings'
    );

    add_settings_field(
        'link_text',
        __('Affiliate Link Text', 'amz-price-updater'),
        'amzpu_render_link_text_field',
        'amzpu-settings',
        'amzpu_affiliate_settings'
    );
}

function amzpu_sanitize_settings($input)
{
    $input = is_array($input) ? $input : [];

    $partner_tag = isset($input['partner_tag'])
        ? sanitize_text_field($input['partner_tag'])
        : '';

    $display_mode = isset($input['display_mode'])
        ? sanitize_key($input['display_mode'])
        : 'prices_coming_soon';

    $allowed_modes = [
        'links_only',
        'prices_coming_soon',
        'live_prices',
    ];

    if (!in_array($display_mode, $allowed_modes, true)) {
        $display_mode = 'prices_coming_soon';
    }

    $link_text = isset($input['link_text'])
        ? sanitize_text_field($input['link_text'])
        : 'View on Amazon';

    if ($link_text === '') {
        $link_text = 'View on Amazon';
    }

    return [
        'partner_tag'  => $partner_tag,
        'display_mode' => $display_mode,
        'link_text'    => $link_text,
    ];
}

/*
|--------------------------------------------------------------------------
| Settings field rendering
|--------------------------------------------------------------------------
*/

function amzpu_render_settings_section()
{
    echo '<p>' .
        esc_html__(
            'Configure the Amazon Associate tag and how the shortcode displays product links. Live prices remain disabled until the API integration has been tested.',
            'amz-price-updater'
        ) .
        '</p>';
}

function amzpu_render_partner_tag_field()
{
    $settings = amzpu_get_settings();

    echo '<input type="text" name="amzpu_settings[partner_tag]" value="' .
        esc_attr($settings['partner_tag']) .
        '" class="regular-text" autocomplete="off" />';

    echo '<p class="description">' .
        esc_html__(
            'Example: yoursite-20. Update this one value later if you need to change your Amazon Associate tracking ID.',
            'amz-price-updater'
        ) .
        '</p>';
}

function amzpu_render_display_mode_field()
{
    $settings = amzpu_get_settings();
    $selected_mode = $settings['display_mode'];

    $options = [
        'links_only'         => __('Links only', 'amz-price-updater'),
        'prices_coming_soon' => __('Prices coming soon', 'amz-price-updater'),
        'live_prices'        => __('Live prices (not enabled yet)', 'amz-price-updater'),
    ];

    echo '<select name="amzpu_settings[display_mode]">';

    foreach ($options as $value => $label) {
        echo '<option value="' . esc_attr($value) . '" ' .
            selected($selected_mode, $value, false) .
            '>' .
            esc_html($label) .
            '</option>';
    }

    echo '</select>';

    echo '<p class="description">' .
        esc_html__(
            'Use “Prices coming soon” while you are building pages. The live-price option currently shows a safe placeholder rather than a numeric price.',
            'amz-price-updater'
        ) .
        '</p>';
}

function amzpu_render_link_text_field()
{
    $settings = amzpu_get_settings();

    echo '<input type="text" name="amzpu_settings[link_text]" value="' .
        esc_attr($settings['link_text']) .
        '" class="regular-text" />';

    echo '<p class="description">' .
        esc_html__(
            'Default: View on Amazon',
            'amz-price-updater'
        ) .
        '</p>';
}

/*
|--------------------------------------------------------------------------
| Settings page rendering
|--------------------------------------------------------------------------
*/

function amzpu_render_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('AMZ Price Updater', 'amz-price-updater') . '</h1>';

    echo '<form method="post" action="options.php">';
    settings_fields('amzpu_settings_group');
    do_settings_sections('amzpu-settings');
    submit_button(__('Save Settings', 'amz-price-updater'));
    echo '</form>';

    echo '<hr>';

    echo '<h2>' . esc_html__('Shortcode Usage', 'amz-price-updater') . '</h2>';
    echo '<p>' .
        esc_html__('Add this shortcode to a post or page, replacing the example ASIN:', 'amz-price-updater') .
        '</p>';
    echo '<code>[amz_price asin="B00X4WHP5E"]</code>';

    echo '</div>';
}
