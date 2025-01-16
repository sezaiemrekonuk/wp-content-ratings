<?php
/*
* Plugin Name: WP Content Ratings
* Plugin URI:
* Description: Add rating points to your posts, pages as you need.
* Version: 0.0.1
* Author: Sezai Emre Konuk
* Author URI: https://www.sezaiemrekonuk.com
* Text Domain: wp-content-ratings
* Domain Path: /languages
*/
// Enqueue custom scripts and styles from /admin and /public
function wp_content_ratings_enqueue_assets()
{
    // public
    wp_enqueue_style('wp-content-ratings', plugin_dir_url(__FILE__) . 'public/css/wp-content-ratings.css', array(), '0.0.1',
        'all');
    wp_enqueue_script('wp-content-ratings', plugin_dir_url(__FILE__) . 'public/js/wp-content-ratings.js', array('jquery'),
        '0.0.1', true);

    // admin
    wp_enqueue_style('wp-content-ratings-admin', plugin_dir_url(__FILE__) . 'admin/css/wp-content-ratings-admin.css',
        array(), '0.0.1', 'all');
    wp_enqueue_script('wp-content-ratings-admin', plugin_dir_url(__FILE__) . 'admin/js/wp-content-ratings-admin.js',
        array('jquery'), '0.0.1', true);
}

add_action('wp_enqueue_scripts', 'wp_content_ratings_enqueue_assets');

// Add Plugin To Admin Menu
function wp_content_ratings_add_admin_menu()
{
    add_menu_page('WP Content Ratings', 'WP Content Ratings', 'manage_options', 'wp-content-ratings',
        'wp_content_ratings_admin_page', 'dashicons-star-filled', 6);
}

add_action('admin_menu', 'wp_content_ratings_add_admin_menu');

// Admin Page
function wp_content_ratings_admin_page()
{
    echo '<h1>WP Content Ratings</h1>';
}