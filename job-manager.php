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
require_once plugin_dir_path( __FILE__ ) . 'includes/class-job-manager-role.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-applicants.php';


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



















