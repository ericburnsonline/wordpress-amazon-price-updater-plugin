<?php
/**
 * Plugin Name: WordPress Amazon Price Updater
 * Description: Experimental plugin that fetches Amazon PA-API pricing and displays it via shortcode.
 * Version: 0.1.0
 * Author: Eric Burns
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Define plugin constants
|--------------------------------------------------------------------------
*/

define('AMZPU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('AMZPU_PLUGIN_URL', plugin_dir_url(__FILE__));
define('AMZPU_VERSION', '0.1.0');


/*
|--------------------------------------------------------------------------
| Load core components
|--------------------------------------------------------------------------
*/

require_once AMZPU_PLUGIN_DIR . 'includes/amz-shortcode.php';


/*
|--------------------------------------------------------------------------
| Load admin settings (only in admin area)
|--------------------------------------------------------------------------
*/

if (is_admin()) {
    require_once AMZPU_PLUGIN_DIR . 'includes/admin-settings.php';
}
