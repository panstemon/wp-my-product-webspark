<?php
/**
 * Plugin Name: WP My Product Webspark
 * Plugin URI: https://yourwebsite.com
 * Description: A custom plugin to extend WooCommerce functionality by adding the ability to perform CRUD operations on products through the My Account page.
 * Version: 1.0.0
 * Author: Bohdan Denysenko
 * Author URI: https://github.com/panstemon
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-my-product-webspark
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 8.1
 * WC requires at least: 9.0.0
 * WC tested up to: 9.7.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WPMPW_VERSION', '1.0.0');

define('WPMPW_MIN_PHP_VERSION', '8.1');
define('WPMPW_MIN_WP_VERSION', '5.8');

define('WPMPW_PATH', plugin_dir_path(__FILE__));
define('WPMPW_BASEDIR', dirname(plugin_basename(__FILE__)));
define('WPMPW_ROOT', str_replace(ABSPATH, '/', WPMPW_PATH));
define('WPMPW_URI', home_url(WPMPW_ROOT));

define('WPMPW_REQUIRED_PLUGINS', [
    'woocommerce/woocommerce.php' => 'WooCommerce',
]);

if (!file_exists(filename: $composer = WPMPW_PATH . '/vendor/autoload.php')) {
    wp_die(__('Error locating autoloader. Please run <code>composer install</code>.', 'wp-my-product-webspark'));
}

require_once $composer;

if (!function_exists('wpmpw')) {
    function wpmpw(): WPMyProductWebspark\App
    {
        return WPMyProductWebspark\App::get();
    }
}

wpmpw();

register_activation_hook(__FILE__, [wpmpw(), 'onActivation']);
register_deactivation_hook(__FILE__, [wpmpw(), 'onDeactivation']);