<?php
/**
 * Misc/Helper functions for WP Content Ratings
 */

if ( ! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

/**
 * Retrieve the current post's rating and display it in stars or numeric form
 * depending on the plugin setting (5 vs. 10).
 *
 * Usage (shortcode): [wpcr_rating]
 */
function wpcr_rating_shortcode( $atts ) {
    // Shortcode attributes (optional, if you want more customization)
    $atts = shortcode_atts( [], $atts, 'wpcr_rating' );

    // If we're not in The Loop, we need a post ID. Attempt to get the current global post.
    global $post;
    if ( ! $post ) {
        return '';
    }

    $post_id = $post->ID;

    // Get the saved rating
    $rating = get_post_meta( $post_id, '_wpcr_editor_rating', true );
    if ( '' === $rating ) {
        // No rating set
        return '<span class="wpcr-no-rating">No editor rating.</span>';
    }

    // Get plugin settings (for rating scale)
    $options      = get_option( 'wpcr_settings' );
    $rating_scale = isset( $options['rating_scale'] ) ? $options['rating_scale'] : '5'; // default to 5

    // Convert rating to an integer
    $rating_val = (int) $rating;

    // Ensure rating doesn’t exceed the chosen scale (just a safeguard)
    if ( $rating_val > (int) $rating_scale ) {
        $rating_val = (int) $rating_scale;
    }

    // Build output
    // 1) Numeric text, e.g. "4/5" or "8/10"
    // 2) Star icons to visually represent the rating
    //    For example, if scale is 5 and rating is 3, show 3 filled stars, 2 empty.

    // (A) Numeric version (for reference)
    $numeric_output = sprintf( '%d / %d', $rating_val, $rating_scale );

    // (B) Star version
    // We'll generate HTML with filled/empty stars
    // For simplicity, we can use dashicons (built into WP) or Unicode stars.
    // Let’s use Unicode ★ for filled and ☆ for empty.

    $editor_name = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );
    $editor_photo = get_avatar( get_post_field( 'post_author', $post_id ), 32 );

    $stars = '';
    for ( $i = 1; $i <= $rating_scale; $i++ ) {
        if ( $i <= $rating_val ) {
            // Filled star
            $stars .= '<span class="wpcr-star filled">&#9733;</span>';
        } else {
            // Empty star
            $stars .= '<span class="wpcr-star empty">&#9734;</span>';
        }
    }

    // Combine them: "Numeric (Star visuals)"
    // Adjust to your preference if you only want stars or only numeric text
//    $output = sprintf(
//        '<div class="wpcr-rating-display">
//            <span class="wpcr-numeric">%s</span>
//            <span class="wpcr-stars">%s</span>
//         </div>',
//        esc_html( $numeric_output ),
//        $stars
//    );
    // Combine them: "Numeric (Star visuals)"
    // Also add author name and photo above
    $output = sprintf(
        '<div class="wpcr-rating-display">
<div class="wpcr-rating-display-row">
            <span class="wpcr-numeric">%s</span>
            <span class="wpcr-stars
            ">%s</span>
            </div>
            <div class="wpcr-rating-display-row">
            <span class="wpcr-author">%s</span>
            <span class="wpcr-author-photo">%s</span>
            </div>',
        esc_html( $numeric_output ),
        $stars,
        esc_html( $editor_name ),
        $editor_photo
    );


    return $output;
}
add_shortcode( 'wpcr_rating', 'wpcr_rating_shortcode' );
