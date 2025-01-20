<?php
/**
 * WPCR_Admin_Page Class
 *
 * This class handles the creation of the WP Content Ratings settings page
 * in the WordPress Admin, and registers/stores plugin settings.
 */

if ( ! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

class WPCR_Admin_Page {

    /**
     * Constructor
     */
    public function __construct() {
        // Hook to add the menu item
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        // Hook to register our settings
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Add a top-level menu item for WP Content Ratings
     */
    public function add_admin_menu() {
        add_menu_page(
            'WP Content Ratings',        // Page title
            'WP Content Ratings',        // Menu title
            'manage_options',            // Capability
            'wpcr-settings',             // Menu slug
            [ $this, 'render_settings_page' ], // Callback
            'dashicons-star-filled',     // Icon (optional)
            66                           // Position in the menu (optional)
        );
    }

    /**
     * Register settings, sections, and fields
     */
    public function register_settings() {
        // Register our main plugin settings array in the options table
        register_setting(
            'wpcr_settings_group',    // Settings group name
            'wpcr_settings'           // Option name (in wp_options)
        );

        // Add a section on our settings page
        add_settings_section(
            'wpcr_general_section',               // Section ID
            'General Settings',                   // Section title
            [ $this, 'general_section_callback' ],// Callback that displays below title
            'wpcr_settings_page'                  // Page slug to attach to
        );

        // Add a field for rating scale
        add_settings_field(
            'wpcr_rating_scale',          // Field ID
            'Rating Scale',               // Field label
            [ $this, 'rating_scale_field_callback' ], // Callback to render the field
            'wpcr_settings_page',         // The page slug we added above
            'wpcr_general_section'        // The section ID where the field should appear
        );
    }

    /**
     * Display text for the general section
     */
    public function general_section_callback() {
        echo '<p>Configure the default rating scale and other plugin defaults.</p>';
    }

    /**
     * Field callback for rating scale
     */
    public function rating_scale_field_callback() {
        // Get the saved options
        $options = get_option( 'wpcr_settings' );
        // If not set, default to '5'
        $current_value = isset( $options['rating_scale'] ) ? $options['rating_scale'] : '5';

        // Render a simple dropdown (5 or 10)
        echo '<select name="wpcr_settings[rating_scale]">';
        echo '<option value="5" ' . selected( $current_value, '5', false ) . '>5 Stars/Points</option>';
        echo '<option value="10" ' . selected( $current_value, '10', false ) . '>10 Stars/Points</option>';
        echo '</select>';
    }

    /**
     * Render the entire settings page
     */
    public function render_settings_page() {
        // Only allow users with the right capability
        if ( ! current_user_can('manage_options') ) {
            return;
        }

        ?>
        <div class="wrap">
            <h1>WP Content Ratings Settings</h1>
            <form method="post" action="options.php">
                <?php
                // Output nonce, action, and option_page fields
                settings_fields( 'wpcr_settings_group' );
                // Display our sections and fields
                do_settings_sections( 'wpcr_settings_page' );
                // Submit button
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
