<?php
/**
 * WPCR_Dashboard Class
 *
 * Handles the "Ratings Overview" admin page, displaying top-rated posts
 * and filters by category or tag.
 */

if ( ! defined('ABSPATH') ) {
    exit; // Exit if accessed directly
}

class WPCR_Dashboard {

    /**
     * Constructor
     */
    public function __construct() {
        // Create a submenu item under "WP Content Ratings" for the dashboard
        add_action( 'admin_menu', [ $this, 'add_dashboard_submenu' ] );
    }

    /**
     * Add a submenu page under the main WP Content Ratings menu
     */
    public function add_dashboard_submenu() {
        add_submenu_page(
            'wpcr-settings',         // Parent slug (the main settings page slug)
            'Ratings Overview',      // Page title
            'Ratings Overview',      // Menu title
            'manage_options',        // Capability
            'wpcr-dashboard',        // Submenu slug
            [ $this, 'render_dashboard_page' ], // Callback
            10                       // Position
        );
    }

    /**
     * Render the "Ratings Overview" page
     */
    public function render_dashboard_page() {
        // Security check
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Handle any filters (category or tag) submitted
        $selected_cat = isset( $_GET['wpcr_category'] ) ? absint( $_GET['wpcr_category'] ) : 0;
        $selected_tag = isset( $_GET['wpcr_tag'] ) ? sanitize_text_field( $_GET['wpcr_tag'] ) : '';

        // We'll retrieve top-rated posts in a custom function (below)
        $top_posts = $this->get_top_rated_posts( $selected_cat, $selected_tag );

        ?>
        <div class="wrap">
            <h1>Ratings Overview</h1>
            <p>Below are your top-rated posts/pages. Use the filters to narrow by category or tag.</p>

            <!-- Filter Form -->
            <form method="get" action="">
                <!-- Keep these inputs to preserve the page slug -->
                <input type="hidden" name="page" value="wpcr-dashboard" />

                <!-- Category Dropdown -->
                <label for="wpcr_category">Filter by Category:</label>
                <?php
                wp_dropdown_categories( [
                    'show_option_all' => 'All Categories',
                    'name'            => 'wpcr_category',
                    'id'              => 'wpcr_category',
                    'selected'        => $selected_cat,
                    'taxonomy'        => 'category',
                    'hide_empty'      => false,
                ] );
                ?>

                <!-- Tag Input (optional, just a simple text) -->
                <label for="wpcr_tag" style="margin-left:20px;">Filter by Tag:</label>
                <input type="text" name="wpcr_tag" id="wpcr_tag"
                       value="<?php echo esc_attr( $selected_tag ); ?>"
                       placeholder="Enter tag slug or name" />

                <input type="submit" class="button button-secondary" value="Filter" />
            </form>
            <br/>

            <!-- Display the results -->
            <?php if ( ! empty( $top_posts ) ) : ?>
                <table class="widefat striped">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Rating</th>
                        <th>Category(s)</th>
                        <th>Tag(s)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ( $top_posts as $post ) :
                        // Grab categories & tags for display
                        $categories = get_the_category( $post->ID );
                        $tags       = get_the_tags( $post->ID );
                        ?>
                        <tr>
                            <td>
                                <a href="<?php echo get_edit_post_link( $post->ID ); ?>">
                                    <?php echo esc_html( get_the_title( $post->ID ) ); ?>
                                </a>
                            </td>
                            <td><?php echo esc_html( get_post_meta( $post->ID, '_wpcr_editor_rating', true ) ); ?></td>
                            <td>
                                <?php
                                if ( ! empty( $categories ) ) {
                                    $cat_names = wp_list_pluck( $categories, 'name' );
                                    echo esc_html( implode( ', ', $cat_names ) );
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ( ! empty( $tags ) ) {
                                    $tag_names = wp_list_pluck( $tags, 'name' );
                                    echo esc_html( implode( ', ', $tag_names ) );
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No top-rated posts found for the selected filters.</p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Query the database for top-rated posts
     *
     * @param int    $category  Category ID to filter by
     * @param string $tag       Tag slug or name to filter by
     * @return array            WP_Post objects
     */
    private function get_top_rated_posts( $category = 0, $tag = '' ) {
        $meta_key = '_wpcr_editor_rating';

        // Build arguments for WP_Query
        $args = [
            'post_type'      => [ 'post', 'page' ],
            'posts_per_page' => 10, // you could make this user-configurable
            'meta_key'       => $meta_key,
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'meta_query'     => [
                [
                    'key'     => $meta_key,
                    'type'    => 'NUMERIC',
                    'compare' => 'EXISTS'
                ]
            ],
        ];

        // If a category is selected, add it to query args
        if ( $category ) {
            $args['cat'] = $category;
        }

        // If a tag is selected, let's handle that
        if ( ! empty( $tag ) ) {
            // We'll assume user entered a tag slug or name
            // You can refine this logic to confirm if it's a valid slug or ID
            $args['tag'] = $tag;
        }

        $query = new WP_Query( $args );
        return $query->posts;
    }
}
