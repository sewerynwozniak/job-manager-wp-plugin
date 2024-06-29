<?php
/**
 * Plugin Name:       Job Manager
 * Description:       Example block scaffolded with Create Block tool.
 * Requires at least: 6.1
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       job-manager
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */

 
 require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-post-type.php';
 require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-fields.php';
 require_once plugin_dir_path( __FILE__ ) . 'includes/class-job-offer-template.php';
 require_once plugin_dir_path( __FILE__ ) . 'includes/class-menu-page.php';


function create_block_job_manager_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'create_block_job_manager_block_init' );








function handle_job_application_submission() {
    // Check if nonce is valid
    if (!isset($_POST['job_application_nonce']) || !wp_verify_nonce($_POST['job_application_nonce'], 'job_application_form')) {
        wp_die('Security check failed');
    }

    // Sanitize and validate form data
    $applicant_name = sanitize_text_field($_POST['applicant_name']);
    $job_offer_id = intval($_POST['job_offer_id']);

    // Check if required fields are not empty
    if (empty($applicant_name) || empty($job_offer_id)) {
        wp_die('Please fill all required fields.');
    }

    // Create a new applicant post
    $applicant_post = array(
        'post_title'   => $applicant_name,
        'post_content' => '',
        'post_status'  => 'publish',
        'post_type'    => 'applicants',
    );

    $applicant_id = wp_insert_post($applicant_post);

    // Add the job offer ID as a custom field to the applicant post
    if (!is_wp_error($applicant_id)) {
        update_post_meta($applicant_id, 'related_job_offer', $job_offer_id);
    }

    // Redirect to a thank you page or back to the job offer page
    if (!is_wp_error($applicant_id)) {
        wp_redirect(get_permalink($job_offer_id));
        exit;
    } else {
        wp_die('Failed to save applicant information.');
    }
}
add_action('admin_post_submit_job_application', 'handle_job_application_submission');
add_action('admin_post_nopriv_submit_job_application', 'handle_job_application_submission');














class Custom_Admin_Page {
    public function __construct() {
        add_action('admin_menu', array($this, 'register_custom_page'));
    }

    public function register_custom_page() {
        add_menu_page(
            'Applicants',
            'Applicants',
            'manage_options',
            'custom-applicants',
            array($this, 'custom_page_content'),
            'dashicons-welcome-learn-more',
            6
        );
    }

    public function custom_page_content() {
        $job_offers = get_posts(array('post_type' => 'job-offers', 'numberposts' => -1));
        $selected_job_offer = isset($_GET['job_offer_filter']) ? intval($_GET['job_offer_filter']) : '';
        ?>
        <div class="wrap">
            <h1><?php _e('Applicants', 'text_domain'); ?></h1>
            <form method="GET" action="">
                <input type="hidden" name="page" value="custom-applicants">
                <select name="job_offer_filter" id="job_offer_filter">
                    <option value=""><?php _e('All Job Offers', 'text_domain'); ?></option>
                    <?php foreach ($job_offers as $job_offer) : ?>
                        <option value="<?php echo esc_attr($job_offer->ID); ?>" <?php selected($selected_job_offer, $job_offer->ID); ?>>
                            <?php echo esc_html($job_offer->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="<?php _e('Filter', 'text_domain'); ?>" class="button">
            </form>

            <?php
            $args = array(
                'post_type' => 'applicants',
                'posts_per_page' => -1,
            );

            if (!empty($selected_job_offer)) {
                $args['meta_query'] = array(
                    array(
                        'key' => 'related_job_offer',
                        'value' => $selected_job_offer,
                        'compare' => '='
                    )
                );
            }

            $applicants = new WP_Query($args);

            if ($applicants->have_posts()) : ?>
                <table class="widefat fixed" cellspacing="0">
                    <thead>
                        <tr>
                            <th id="title" class="manage-column column-title column-primary"><?php _e('Name', 'text_domain'); ?></th>
                            <th id="email" class="manage-column"><?php _e('Email', 'text_domain'); ?></th>
                            <th id="job_offer" class="manage-column"><?php _e('Job Offer', 'text_domain'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($applicants->have_posts()) : $applicants->the_post(); ?>
                            <tr>
                                <td class="title column-title has-row-actions column-primary">
                                    <strong><?php the_title(); ?></strong>
                                    <div class="row-actions">
                                        <span class="edit"><a href="<?php echo get_edit_post_link(); ?>"><?php _e('Edit', 'text_domain'); ?></a></span>
                                    </div>
                                </td>
                                <td><?php echo esc_html(get_post_meta(get_the_ID(), 'email', true)); ?></td>
                                <td>
                                    <?php
                                    $related_job_offer = get_post_meta(get_the_ID(), 'related_job_offer', true);
                                    if ($related_job_offer) {
                                        echo esc_html(get_the_title($related_job_offer));
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php _e('No applicants found.', 'text_domain'); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }
}

new Custom_Admin_Page();




class Applicant_Meta_Box {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_meta_box_data'));
    }

    public function add_meta_box() {
        add_meta_box(
            'related_job_offer',
            __('Related Job Offer', 'text_domain'),
            array($this, 'render_meta_box'),
            'applicants',
            'side',
            'default'
        );
    }

    public function render_meta_box($post) {
        wp_nonce_field('related_job_offer_meta_box', 'related_job_offer_meta_box_nonce');
        $value = get_post_meta($post->ID, 'related_job_offer', true);
        $job_offers = get_posts(array('post_type' => 'job-offers', 'numberposts' => -1));
        ?>
        <label for="related_job_offer"><?php _e('Select Job Offer:', 'text_domain'); ?></label>
        <select name="related_job_offer" id="related_job_offer">
            <option value=""><?php _e('None', 'text_domain'); ?></option>
            <?php foreach ($job_offers as $job_offer) : ?>
                <option value="<?php echo esc_attr($job_offer->ID); ?>" <?php selected($value, $job_offer->ID); ?>>
                    <?php echo esc_html($job_offer->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function save_meta_box_data($post_id) {
        if (!isset($_POST['related_job_offer_meta_box_nonce']) || !wp_verify_nonce($_POST['related_job_offer_meta_box_nonce'], 'related_job_offer_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['related_job_offer'])) {
            update_post_meta($post_id, 'related_job_offer', sanitize_text_field($_POST['related_job_offer']));
        }
    }
}

new Applicant_Meta_Box();
