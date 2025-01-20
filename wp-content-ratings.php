<?php
/**
 * Plugin Name: WP Content Ratings
 * Plugin URI:  https://sezaiemrekonuk.com/wp-content-ratings
 * Description: Add an Editor/Admin rating field to posts/pages, and view top-rated content in the admin.
 * Version:     1.0
 * Author:      Sezai Emre Konuk
 * Author URI:  https://sezaiemrekonuk.com
 * License:     GPL2
 * Text Domain: wp-content-ratings
 */

// Exit if accessed directly.
if ( ! defined('ABSPATH') ) {
    exit;
}

/**
 * Main plugin class or bootstrap file will go here.
 */

// Let's define a version constant for reference
if ( ! defined('WPCR_VERSION') ) {
    define('WPCR_VERSION', '1.0');
}

/**
 * Includes
 * Load our classes or helper files here
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-wpcr-metabox.php';

// Instantiate the Metabox class to initialize it
function wpcr_init_metabox() {
    new WPCR_Metabox();
}
add_action('plugins_loaded', 'wpcr_init_metabox');

// Includes
require_once plugin_dir_path(__FILE__) . 'includes/class-wpcr-metabox.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wpcr-admin-page.php';


add_action('plugins_loaded', 'wpcr_init_classes');


// Includes
require_once plugin_dir_path(__FILE__) . 'includes/class-wpcr-metabox.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wpcr-admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-wpcr-dashboard.php'; // NEW

// Instantiate classes
function wpcr_init_classes() {
    new WPCR_Metabox();
    new WPCR_Admin_Page();
    new WPCR_Dashboard(); // NEW
}
add_action('plugins_loaded', 'wpcr_init_classes');

// Includes
require_once plugin_dir_path(__FILE__) . 'includes/wpcr-functions.php'; // new!

/**
 * Enqueue front-end CSS
 */
function wpcr_enqueue_frontend_css() {
    // Only load on the front end (not admin)
    if ( ! is_admin() ) {
        wp_enqueue_style(
            'wpcr-styles',
            plugin_dir_url(__FILE__) . 'assets/css/wpcr-style.css',
            [],
            WPCR_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'wpcr_enqueue_frontend_css');
