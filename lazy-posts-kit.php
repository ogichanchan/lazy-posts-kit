<?php
/**
 * Plugin Name: Lazy Posts Kit
 * Plugin URI: https://github.com/ogichanchan/lazy-posts-kit
 * Description: A unique PHP-only WordPress utility. A lazy style posts plugin acting as a kit. Focused on simplicity and efficiency.
 * Version: 1.0.0
 * Author: ogichanchan
 * Author URI: https://github.com/ogichanchan
 * License: GPLv2 or later
 * Text Domain: lazy-posts-kit
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin class.
 * Manages plugin initialization, settings, and shortcode functionality.
 */
class Lazy_Posts_Kit {

    /**
     * Option key for plugin settings.
     *
     * @var string
     */
    private $option_key = 'lazy_posts_kit_settings';

    /**
     * Default plugin settings.
     *
     * @var array
     */
    private $default_settings = array(
        'posts_per_page' => 5,
        'post_types'     => 'post', // Comma-separated post types.
        'show_thumbnail' => true,
        'show_excerpt'   => true,
        'show_date'      => true,
        'title_tag'      => 'h2', // HTML tag for post titles.
    );

    /**
     * Constructor.
     * Registers hooks for activation, deactivation, admin menu, settings,
     * shortcode, and inline styles.
     */
    public function __construct() {
        // Register activation and deactivation hooks.
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        // Add admin menu and settings.
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );

        // Register shortcode.
        add_action( 'init', array( $this, 'register_shortcode' ) );

        // Inject inline styles for admin and frontend.
        add_action( 'admin_head', array( $this, 'admin_inline_styles' ) );
        add_action( 'wp_head', array( $this, 'frontend_inline_styles' ) );
    }

    /**
     * Plugin activation logic.
     * Sets default settings if they don't already exist.
     */
    public function activate() {
        add_option( $this->option_key, $this->default_settings );
    }

    /**
     * Plugin deactivation logic.
     * Currently does nothing, but can be used for cleanup (e.g., deleting options).
     */
    public function deactivate() {
        // Optionally delete plugin settings on deactivation:
        // delete_option( $this->option_key );
    }

    /**
     * Adds the plugin settings page to the WordPress admin menu under 'Settings'.
     */
    public function add_admin_menu() {
        add_options_page(
            esc_html__( 'Lazy Posts Kit Settings', 'lazy-posts-kit' ),
            esc_html__( 'Lazy Posts Kit', 'lazy-posts-kit' ),
            'manage_options',
            'lazy-posts-kit',
            array( $this, 'render_admin_page' )
        );
    }

    /**
     * Registers plugin settings and fields using the WordPress Settings API.
     */
    public function register_settings() {
        register_setting(
            $this->option_key, // Option group name.
            $this->option_key, // Option name.
            array( $this, 'sanitize_settings' ) // Sanitize callback.
        );

        add_settings_section(
            'lazy_posts_kit_general_section', // ID for the section.
            esc_html__( 'General Settings', 'lazy-posts-kit' ), // Title of the section.
            array( $this, 'render_general_section_callback' ), // Callback to render section description.
            'lazy-posts-kit' // Page slug to display this section on.
        );

        add_settings_field(
            'posts_per_page', // ID for the field.
            esc_html__( 'Default Posts Per Page', 'lazy-posts-kit' ), // Title of the field.
            array( $this, 'render_posts_per_page_field' ), // Callback to render the field input.
            'lazy-posts-kit', // Page slug.
            'lazy_posts_kit_general_section' // Section ID.
        );

        add_settings_field(
            'post_types',
            esc_html__( 'Default Post Types', 'lazy-posts-kit' ),
            array( $this, 'render_post_types_field' ),
            'lazy-posts-kit',
            'lazy_posts_kit_general_section'
        );

        add_settings_field(
            'show_thumbnail',
            esc_html__( 'Show Post Thumbnail', 'lazy-posts-kit' ),
            array( $this, 'render_show_thumbnail_field' ),
            'lazy-posts-kit',
            'lazy_posts_kit_general_section'
        );

        add_settings_field(
            'show_excerpt',
            esc_html__( 'Show Post Excerpt', 'lazy-posts-kit' ),
            array( $this, 'render_show_excerpt_field' ),
            'lazy-posts-kit',
            'lazy_posts_kit_general_section'
        );

        add_settings_field(
            'show_date',
            esc_html__( 'Show Post Date', 'lazy-posts-kit' ),
            array( $this, 'render_show_date_field' ),
            'lazy-posts-kit',
            'lazy_posts_kit_general_section'
        );

        add_settings_field(
            'title_tag',
            esc_html__( 'Default Title Tag', 'lazy-posts-kit' ),
            array( $this, 'render_title_tag_field' ),
            'lazy-posts-kit',
            'lazy_posts_kit_general_section'
        );
    }

    /**
     * Renders the description for the general settings section.
     */
    public function render_general_section_callback() {
        echo '<p>' . esc_html__( 'Configure the default display options for the [lazy_posts_kit] shortcode.', 'lazy-posts-kit' ) . '</p>';
    }

    /**
     * Renders the input field for 'Default Posts Per Page'.
     */
    public function render_posts_per_page_field() {
        $settings = $this->get_settings();
        printf(
            '<input type="number" name="%s[posts_per_page]" value="%s" min="1" />',
            esc_attr( $this->option_key ),
            esc_attr( $settings['posts_per_page'] )
        );
        echo '<p class="description">' . esc_html__( 'The default number of posts to display.', 'lazy-posts-kit' ) . '</p>';
    }

    /**
     * Renders the input field for 'Default Post Types'.
     */
    public function render_post_types_field() {
        $settings = $this->get_settings();
        printf(
            '<input type="text" name="%s[post_types]" value="%s" class="regular-text" />',
            esc_attr( $this->option_key ),
            esc_attr( $settings['post_types'] )
        );
        echo '<p class="description">' . esc_html__( 'Comma-separated list of post types (e.g., post,page,product).', 'lazy-posts-kit' ) . '</p>';
    }

    /**
     * Renders the checkbox for 'Show Post Thumbnail'.
     */
    public function render_show_thumbnail_field() {
        $settings = $this->get_settings();
        $checked  = checked( 1, $settings['show_thumbnail'], false );
        printf(
            '<label><input type="checkbox" name="%s[show_thumbnail]" value="1" %s /> %s</label>',
            esc_attr( $this->option_key ),
            $checked,
            esc_html__( 'Display featured image/thumbnail.', 'lazy-posts-kit' )
        );
    }

    /**
     * Renders the checkbox for 'Show Post Excerpt'.
     */
    public function render_show_excerpt_field() {
        $settings = $this->get_settings();
        $checked  = checked( 1, $settings['show_excerpt'], false );
        printf(
            '<label><input type="checkbox" name="%s[show_excerpt]" value="1" %s /> %s</label>',
            esc_attr( $this->option_key ),
            $checked,
            esc_html__( 'Display post excerpt.', 'lazy-posts-kit' )
        );
    }

    /**
     * Renders the checkbox for 'Show Post Date'.
     */
    public function render_show_date_field() {
        $settings = $this->get_settings();
        $checked  = checked( 1, $settings['show_date'], false );
        printf(
            '<label><input type="checkbox" name="%s[show_date]" value="1" %s /> %s</label>',
            esc_attr( $this->option_key ),
            $checked,
            esc_html__( 'Display post publication date.', 'lazy-posts-kit' )
        );
    }

    /**
     * Renders the dropdown for 'Default Title Tag'.
     */
    public function render_title_tag_field() {
        $settings     = $this->get_settings();
        $allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span' );
        echo '<select name="' . esc_attr( $this->option_key ) . '[title_tag]">';
        foreach ( $allowed_tags as $tag ) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr( $tag ),
                selected( $tag, $settings['title_tag'], false ),
                esc_html( $tag )
            );
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__( 'HTML tag to use for post titles.', 'lazy-posts-kit' ) . '</p>';
    }

    /**
     * Sanitizes and validates plugin settings before saving.
     *
     * @param array $input The raw input array from the form.
     * @return array The sanitized settings array.
     */
    public function sanitize_settings( $input ) {
        // Start with current settings to preserve values for unchecked checkboxes.
        $sanitized_input = $this->get_settings();

        // Sanitize posts_per_page.
        if ( isset( $input['posts_per_page'] ) ) {
            $sanitized_input['posts_per_page'] = absint( $input['posts_per_page'] );
            if ( $sanitized_input['posts_per_page'] < 1 ) {
                $sanitized_input['posts_per_page'] = 1;
            }
        }

        // Sanitize post_types (comma-separated list).
        if ( isset( $input['post_types'] ) ) {
            $post_types_raw = explode( ',', sanitize_text_field( $input['post_types'] ) );
            $sanitized_post_types = array_map( 'trim', $post_types_raw );
            $sanitized_post_types = array_filter( $sanitized_post_types ); // Remove empty entries.
            $sanitized_input['post_types'] = implode( ',', $sanitized_post_types );
            if ( empty( $sanitized_input['post_types'] ) ) { // Ensure at least 'post' if list is empty.
                $sanitized_input['post_types'] = 'post';
            }
        }

        // Sanitize checkboxes: set to 1 if checked, 0 otherwise.
        $sanitized_input['show_thumbnail'] = isset( $input['show_thumbnail'] ) ? 1 : 0;
        $sanitized_input['show_excerpt']   = isset( $input['show_excerpt'] ) ? 1 : 0;
        $sanitized_input['show_date']      = isset( $input['show_date'] ) ? 1 : 0;

        // Sanitize title_tag.
        if ( isset( $input['title_tag'] ) ) {
            $allowed_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span' );
            if ( in_array( $input['title_tag'], $allowed_tags, true ) ) {
                $sanitized_input['title_tag'] = sanitize_text_field( $input['title_tag'] );
            } else {
                $sanitized_input['title_tag'] = $this->default_settings['title_tag']; // Fallback to default.
            }
        }

        return $sanitized_input;
    }

    /**
     * Retrieves current plugin settings, merged with defaults to ensure all keys exist.
     *
     * @return array Current plugin settings.
     */
    private function get_settings() {
        return wp_parse_args( get_option( $this->option_key, array() ), $this->default_settings );
    }

    /**
     * Renders the plugin's admin settings page.
     */
    public function render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <div class="wrap lazy-posts-kit-admin">
            <h1><?php esc_html_e( 'Lazy Posts Kit Settings', 'lazy-posts-kit' ); ?></h1>
            <p><?php esc_html_e( 'Configure the default behavior for the [lazy_posts_kit] shortcode.', 'lazy-posts-kit' ); ?></p>
            <form action="options.php" method="post">
                <?php
                // Output necessary hidden fields and nonces for the settings page.
                settings_fields( $this->option_key );
                // Output all registered settings sections and fields for the 'lazy-posts-kit' page.
                do_settings_sections( 'lazy-posts-kit' );
                // Output the save button.
                submit_button( esc_html__( 'Save Changes', 'lazy-posts-kit' ) );
                ?>
            </form>

            <div class="lazy-posts-kit-shortcode-info">
                <h2><?php esc_html_e( 'Shortcode Usage', 'lazy-posts-kit' ); ?></h2>
                <p><?php esc_html_e( 'Use the following shortcode to display a list of posts:', 'lazy-posts-kit' ); ?></p>
                <code>[lazy_posts_kit]</code>
                <p><?php esc_html_e( 'You can override default settings with the following attributes:', 'lazy-posts-kit' ); ?></p>
                <ul>
                    <li><code>posts_per_page="3"</code></li>
                    <li><code>post_type="page,my_custom_post_type"</code></li>
                    <li><code>order_by="title"</code> (e.g., date, title, rand)</li>
                    <li><code>order="ASC"</code> (e.g., ASC, DESC)</li>
                    <li><code>show_thumbnail="false"</code></li>
                    <li><code>show_excerpt="false"</code></li>
                    <li><code>show_date="false"</code></li>
                    <li><code>title_tag="h3"</code> (e.g., h1-h6, p, div, span)</li>
                </ul>
                <p><?php esc_html_e( 'Example:', 'lazy-posts-kit' ); ?></p>
                <code>[lazy_posts_kit posts_per_page="3" post_type="post,page" order_by="rand" show_thumbnail="true" show_date="true" title_tag="h3"]</code>
            </div>
        </div>
        <?php
    }

    /**
     * Registers the '[lazy_posts_kit]' shortcode.
     */
    public function register_shortcode() {
        add_shortcode( 'lazy_posts_kit', array( $this, 'lazy_posts_kit_shortcode' ) );
    }

    /**
     * Shortcode callback function to display a list of posts based on settings and attributes.
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output of the post list.
     */
    public function lazy_posts_kit_shortcode( $atts ) {
        // Merge with plugin settings and default shortcode attributes.
        $plugin_settings = $this->get_settings();

        $default_atts = array(
            'posts_per_page' => $plugin_settings['posts_per_page'],
            'post_type'      => $plugin_settings['post_types'],
            'order_by'       => 'date',
            'order'          => 'DESC',
            'show_thumbnail' => $plugin_settings['show_thumbnail'],
            'show_excerpt'   => $plugin_settings['show_excerpt'],
            'show_date'      => $plugin_settings['show_date'],
            'title_tag'      => $plugin_settings['title_tag'],
        );

        $atts = shortcode_atts( $default_atts, $atts, 'lazy_posts_kit' );

        // Sanitize and validate shortcode attributes.
        $posts_per_page = absint( $atts['posts_per_page'] );
        if ( $posts_per_page < 1 ) {
            $posts_per_page = 1;
        }

        $post_types_raw = explode( ',', sanitize_text_field( $atts['post_type'] ) );
        $post_types     = array_map( 'trim', $post_types_raw );
        $post_types     = array_filter( $post_types );
        if ( empty( $post_types ) ) {
            $post_types = array( 'post' );
        }

        $order_by_allowed = array( 'none', 'ID', 'author', 'title', 'name', 'type', 'date', 'modified', 'parent', 'rand', 'comment_count', 'menu_order' );
        $order_by         = in_array( $atts['order_by'], $order_by_allowed, true ) ? sanitize_text_field( $atts['order_by'] ) : 'date';

        $order_allowed = array( 'ASC', 'DESC' );
        $order         = in_array( strtoupper( $atts['order'] ), $order_allowed, true ) ? strtoupper( sanitize_text_field( $atts['order'] ) ) : 'DESC';

        // Filter_var is used for boolean-like values from string attributes ('true'/'false').
        $show_thumbnail = filter_var( $atts['show_thumbnail'], FILTER_VALIDATE_BOOLEAN );
        $show_excerpt   = filter_var( $atts['show_excerpt'], FILTER_VALIDATE_BOOLEAN );
        $show_date      = filter_var( $atts['show_date'], FILTER_VALIDATE_BOOLEAN );

        $title_tag_allowed = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'div', 'span' );
        $title_tag         = in_array( $atts['title_tag'], $title_tag_allowed, true ) ? sanitize_text_field( $atts['title_tag'] ) : 'h2';

        $args = array(
            'posts_per_page' => $posts_per_page,
            'post_type'      => $post_types,
            'orderby'        => $order_by,
            'order'          => $order,
            'post_status'    => 'publish',
            'suppress_filters' => false, // Allow other plugins to filter the query.
            'no_found_rows'    => true, // Optimization: tells WP_Query not to count total rows.
        );

        $query = new WP_Query( $args );

        ob_start(); // Start output buffering.

        if ( $query->have_posts() ) {
            echo '<div class="lazy-posts-kit-container">';
            while ( $query->have_posts() ) {
                $query->the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class( 'lazy-posts-kit-item' ); ?>>
                    <?php if ( $show_thumbnail && has_post_thumbnail() ) : ?>
                        <div class="lazy-posts-kit-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail( 'medium' ); ?>
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="lazy-posts-kit-content">
                        <<?php echo esc_html( $title_tag ); ?> class="lazy-posts-kit-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </<?php echo esc_html( $title_tag ); ?>>

                        <?php if ( $show_date ) : ?>
                            <div class="lazy-posts-kit-date">
                                <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                                    <?php echo esc_html( get_the_date() ); ?>
                                </time>
                            </div>
                        <?php endif; ?>

                        <?php if ( $show_excerpt ) : ?>
                            <div class="lazy-posts-kit-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                        <?php endif; ?>
                        <p class="lazy-posts-kit-read-more">
                            <a href="<?php the_permalink(); ?>">
                                <?php esc_html_e( 'Read More &raquo;', 'lazy-posts-kit' ); ?>
                            </a>
                        </p>
                    </div>
                </article>
                <?php
            }
            echo '</div>'; // .lazy-posts-kit-container
            wp_reset_postdata(); // Restore original post data.
        } else {
            echo '<p class="lazy-posts-kit-no-posts">' . esc_html__( 'No posts found.', 'lazy-posts-kit' ) . '</p>';
        }

        return ob_get_clean(); // Return buffered content.
    }

    /**
     * Injects inline CSS for the admin panel.
     */
    public function admin_inline_styles() {
        // This ensures the styles are only added on our plugin's admin page.
        if ( ! isset( $_GET['page'] ) || 'lazy-posts-kit' !== $_GET['page'] ) {
            return;
        }
        ?>
        <style type="text/css">
            .lazy-posts-kit-admin .lazy-posts-kit-shortcode-info {
                background-color: #f0f0f0;
                border: 1px solid #ccc;
                padding: 15px;
                margin-top: 20px;
                border-radius: 5px;
            }
            .lazy-posts-kit-admin .lazy-posts-kit-shortcode-info h2 {
                margin-top: 0;
                font-size: 1.3em;
            }
            .lazy-posts-kit-admin .lazy-posts-kit-shortcode-info code {
                display: inline-block;
                background-color: #e5e5e5;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: monospace;
                font-size: 0.9em;
                border: 1px solid #ddd;
            }
            .lazy-posts-kit-admin .lazy-posts-kit-shortcode-info ul {
                list-style: disc;
                margin-left: 20px;
            }
            .lazy-posts-kit-admin .lazy-posts-kit-shortcode-info ul li {
                margin-bottom: 5px;
            }
            .lazy-posts-kit-admin .form-table th {
                width: 200px;
            }
        </style>
        <?php
    }

    /**
     * Injects inline CSS for the shortcode output on the frontend.
     * Only adds styles if the shortcode is present on the current page/post.
     */
    public function frontend_inline_styles() {
        // Check if the current post content contains our shortcode before outputting styles.
        global $post;
        if ( ! is_a( $post, 'WP_Post' ) || ! has_shortcode( $post->post_content, 'lazy_posts_kit' ) ) {
            return;
        }

        ?>
        <style type="text/css">
            .lazy-posts-kit-container {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
                margin-bottom: 20px;
            }
            .lazy-posts-kit-item {
                border: 1px solid #eee;
                padding: 15px;
                border-radius: 5px;
                background-color: #fff;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                display: flex;
                flex-direction: column;
                transition: transform 0.2s ease-in-out;
            }
            .lazy-posts-kit-item:hover {
                transform: translateY(-3px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.08);
            }
            .lazy-posts-kit-item .lazy-posts-kit-thumbnail {
                margin-bottom: 10px;
            }
            .lazy-posts-kit-item .lazy-posts-kit-thumbnail img {
                max-width: 100%;
                height: auto;
                display: block;
                border-radius: 3px;
            }
            .lazy-posts-kit-item .lazy-posts-kit-content {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
            }
            .lazy-posts-kit-item .lazy-posts-kit-title {
                margin-top: 0;
                margin-bottom: 5px;
                font-size: 1.3em;
                line-height: 1.3;
            }
            .lazy-posts-kit-item .lazy-posts-kit-title a {
                text-decoration: none;
                color: #333;
            }
            .lazy-posts-kit-item .lazy-posts-kit-title a:hover {
                color: #0073aa;
            }
            .lazy-posts-kit-item .lazy-posts-kit-date {
                font-size: 0.85em;
                color: #777;
                margin-bottom: 10px;
            }
            .lazy-posts-kit-item .lazy-posts-kit-excerpt {
                margin-bottom: 10px;
            }
            .lazy-posts-kit-item .lazy-posts-kit-excerpt p:last-child {
                margin-bottom: 0;
            }
            .lazy-posts-kit-item .lazy-posts-kit-read-more {
                margin-top: auto; /* Pushes the read more link to the bottom */
                padding-top: 10px; /* Add some space if needed */
            }
            .lazy-posts-kit-item .lazy-posts-kit-read-more a {
                color: #0073aa;
                text-decoration: none;
            }
            .lazy-posts-kit-item .lazy-posts-kit-read-more a:hover {
                text-decoration: underline;
            }
            .lazy-posts-kit-no-posts {
                padding: 20px;
                border: 1px solid #f0f0f0;
                background-color: #fff;
                text-align: center;
                border-radius: 5px;
            }
        </style>
        <?php
    }
}

// Initialize the plugin by creating an instance of the class.
new Lazy_Posts_Kit();