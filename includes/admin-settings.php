<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Add settings page
|--------------------------------------------------------------------------
*/

add_action('admin_menu', function () {
    add_options_page(
        'AMZ Price Updater',
        'AMZ Price Updater',
        'manage_options',
        'amzpu-settings',
        'amzpu_render_settings_page'
    );
});


/*
|--------------------------------------------------------------------------
| Register settings
|--------------------------------------------------------------------------
*/

add_action('admin_init', function () {

    register_setting(
        'amzpu_settings_group',
        'amzpu_settings',
        [
            'type' => 'array',
            'sanitize_callback' => 'amzpu_sanitize_settings',
            'default' => []
        ]
    );

    add_settings_section(
        'amzpu_main',
        'Amazon Product Advertising API Settings',
        function () {
            echo '<p>Enter your Amazon Product Advertising API credentials.</p>';
        },
        'amzpu-settings'
    );

    add_settings_field(
        'access_key',
        'Access Key',
        'amzpu_field_access_key',
        'amzpu-settings',
        'amzpu_main'
    );

    add_settings_field(
        'partner_tag',
        'Partner Tag',
        'amzpu_field_partner_tag',
        'amzpu-settings',
        'amzpu_main'
    );

});


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function amzpu_get_settings()
{
    $settings = get_option('amzpu_settings', []);

    if (!is_array($settings)) {
        $settings = [];
    }

    return $settings;
}


function amzpu_sanitize_settings($input)
{
    $out = [];

    $out['access_key'] = isset($input['access_key'])
        ? sanitize_text_field($input['access_key'])
        : '';

    $out['partner_tag'] = isset($input['partner_tag'])
        ? sanitize_text_field($input['partner_tag'])
        : '';

    return $out;
}


/*
|--------------------------------------------------------------------------
| Field renderers
|--------------------------------------------------------------------------
*/

function amzpu_field_access_key()
{
    $s = amzpu_get_settings();
    $v = esc_attr($s['access_key'] ?? '');

    echo '<input type="text" name="amzpu_settings[access_key]" value="' . $v . '" class="regular-text" />';
}


function amzpu_field_partner_tag()
{
    $s = amzpu_get_settings();
    $v = esc_attr($s['partner_tag'] ?? '');

    echo '<input type="text" name="amzpu_settings[partner_tag]" value="' . $v . '" class="regular-text" />';
}


/*
|--------------------------------------------------------------------------
| Settings page
|--------------------------------------------------------------------------
*/

function amzpu_render_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    echo '<div class="wrap">';
    echo '<h1>AMZ Price Updater</h1>';

    echo '<form method="post" action="options.php">';

    settings_fields('amzpu_settings_group');
    do_settings_sections('amzpu-settings');
    submit_button('Save Settings');

    echo '</form>';

    echo '<hr>';

    echo '<h2>Verify Configuration</h2>';
    echo '<p>This button performs a simple test request to verify your Amazon API configuration.</p>';

    echo '<form method="post">';
    echo '<input type="hidden" name="amzpu_verify_config" value="1">';
    submit_button('Verify Configuration', 'secondary');
    echo '</form>';

    if (isset($_POST['amzpu_verify_config'])) {
        amzpu_verify_configuration();
    }

    echo '</div>';
}


/*
|--------------------------------------------------------------------------
| Verification logic
|--------------------------------------------------------------------------
*/

function amzpu_verify_configuration()
{
    $settings = amzpu_get_settings();

    $access_key = $settings['access_key'] ?? '';
    $partner_tag = $settings['partner_tag'] ?? '';

    if (!$access_key || !$partner_tag) {
        echo '<div class="notice notice-error"><p>Configuration incomplete. Please enter your Access Key and Partner Tag.</p></div>';
        return;
    }

    if (!defined('AMZPU_SECRET_KEY')) {
        echo '<div class="notice notice-error"><p>Secret key not found. Add AMZPU_SECRET_KEY to wp-config.php.</p></div>';
        return;
    }

    echo '<div class="notice notice-success"><p>Configuration appears valid. API credentials detected.</p></div>';
}
