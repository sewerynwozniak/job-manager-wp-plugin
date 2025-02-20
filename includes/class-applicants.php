<?php

if (!defined('ABSPATH')) {
    exit;
}


class Applicants {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'create_aplicants_submenu'));
        add_action('admin_post_delete_application', array($this, 'delete_application')); 
    }


    public function create_aplicants_submenu() {


        add_submenu_page(
            'job-management',
            'Applicants',
            'Applicants',
            'manage_job_management',
            'custom-applicants',
            array($this, 'render_applicants_page')
        );
    }



    public function render_applicants_page() {
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
                            <th id="actions" class="manage-column"><?php _e('Actions', 'text_domain'); ?></th> <!-- ✅ Added Actions Column -->
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
                                <td>
                                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                                        <?php wp_nonce_field('delete_application_' . get_the_ID(), 'delete_application_nonce'); ?>
                                        <input type="hidden" name="action" value="delete_application">
                                        <input type="hidden" name="application_id" value="<?php echo get_the_ID(); ?>">
                                        <button type="submit" class="button button-danger"><?php _e('Delete', 'text_domain'); ?></button>
                                    </form>
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

    // ✅ Handle Application Deletion
    public function delete_application() {

        if (!wp_get_referer() || strpos(wp_get_referer(), 'admin.php?page=custom-applicants') === false) {
            wp_die(__('Invalid referrer.', 'text_domain'));
        }

        if (!isset($_POST['application_id']) || !isset($_POST['delete_application_nonce'])) {
            wp_die(__('Invalid request.', 'text_domain'));
        }

        $application_id = intval($_POST['application_id']);

        // ✅ Verify Nonce
        if (!wp_verify_nonce($_POST['delete_application_nonce'], 'delete_application_' . $application_id)) {
            wp_die(__('Security check failed.', 'text_domain'));
        }

        // ✅ Check Permission
        if (!current_user_can('manage_job_management')) {
            wp_die(__('You do not have permission to delete applications.', 'text_domain'));
        }

        // ✅ Delete the post
        if (wp_delete_post($application_id, true)) {
            // ✅ Redirect back with success message
            wp_redirect(admin_url('admin.php?page=custom-applicants&deleted=true'));
            exit;
        } else {
            wp_die(__('Failed to delete the application.', 'text_domain'));
        }
    }


}

new Applicants;