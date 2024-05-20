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


function create_block_job_manager_block_init() {
	register_block_type( __DIR__ . '/build' );
}
add_action( 'init', 'create_block_job_manager_block_init' );






// Filter the single template for job offers
// function load_job_offer_single_template( $template ) {
//     global $post;

//     if ( 'job-offer' === $post->post_type ) {
//         return plugin_dir_path( __FILE__ ) . 'templates/single-job-offer.php';
//     }

//     return $template;
// }
// add_filter( 'single_template', 'load_job_offer_single_template' );

