<?php
/**
 * Plugin Name: WordPress Amazon Price Updater
 * Description: Experimental plugin that renders Amazon Associate links from a shortcode.
 * Version: 0.2.0
 * Author: Eric Burns
 * License: GPL-2.0-or-later
 * Text Domain: amz-price-updater
 */

if (!defined('ABSPATH')) {
    exit;
}

define('AMZPU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AMZPU_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AMZPU_VERSION', '0.2.0');

require_once AMZPU_PLUGIN_DIR . 'includes/settings.php';
require_once AMZPU_PLUGIN_DIR . 'includes/amz-shortcode.php';

if (is_admin()) {
    require_once AMZPU_PLUGIN_DIR . 'includes/admin-settings.php';
}
