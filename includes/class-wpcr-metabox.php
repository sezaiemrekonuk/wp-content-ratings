<?php
/**
 * WPCR_Metabox Class
 *
 * This class handles adding a custom metabox to the post/page editor
 * for storing the Editor/Admin rating.
 */

if ( ! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

class WPCR_Metabox {

    /**
     * Constructor: set up hooks
     */
    public function __construct() {
        // Hook into WordPress to add the metabox
        add_action( 'add_meta_boxes', [ $this, 'add_rating_metabox' ] );
        // Hook into the save_post action to save the rating
        add_action( 'save_post', [ $this, 'save_rating_metabox' ] );
    }

    /**
     * Register the metabox
     */
    public function add_rating_metabox() {
        add_meta_box(
            'wpcr_editor_rating',          // Metabox ID
            'Editor Rating',               // Title
            [ $this, 'render_metabox' ],   // Callback to render HTML
            [ 'post', 'page' ],            // Post types where it should appear
            'side',                        // Context (normal, side, advanced)
            'default'                      // Priority
        );
    }

    /**
     * Render the content of the metabox
     */
    public function render_metabox( $post ) {
        // We'll use post meta to store the rating; default to empty if none found
        $rating = get_post_meta( $post->ID, '_wpcr_editor_rating', true );

        // Use a nonce field for security
        wp_nonce_field( 'wpcr_save_editor_rating', 'wpcr_editor_rating_nonce' );

        // Simple input field for rating (can customize to stars if you want)
        echo '<label for="wpcr_editor_rating_field">';
        echo 'Set an Editor Rating (0-10 recommended, or 0-5 if you prefer):';
        echo '</label><br /><br />';
        echo '<input type="number" 
                     step="1" 
                     min="0" 
                     max="10" 
                     id="wpcr_editor_rating_field" 
                     name="wpcr_editor_rating_field" 
                     value="' . esc_attr( $rating ) . '" 
                     style="width:80%;" 
               />';
    }

    /**
     * Save the rating to the database
     */
    public function save_rating_metabox( $post_id ) {
        // Check if our nonce is set
        if ( ! isset( $_POST['wpcr_editor_rating_nonce'] ) ) {
            return;
        }
        // Verify the nonce is valid
        if ( ! wp_verify_nonce( $_POST['wpcr_editor_rating_nonce'], 'wpcr_save_editor_rating' ) ) {
            return;
        }
        // Check for autosave or user permissions
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Make sure our field is present
        if ( isset( $_POST['wpcr_editor_rating_field'] ) ) {
            $rating = sanitize_text_field( $_POST['wpcr_editor_rating_field'] );
            update_post_meta( $post_id, '_wpcr_editor_rating', $rating );
        }
    }
}
