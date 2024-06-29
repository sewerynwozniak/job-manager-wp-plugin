<?php




class Custom_Admin_Menu {
    public function __construct() {
        add_action('admin_menu', array($this, 'create_admin_menu'));
    }

    public function create_admin_menu() {
        // Add main menu page
        add_menu_page(
            'Job Management',    // Page title
            'Jobs',              // Menu title
            'manage_options',    // Capability
            'job-management',    // Menu slug
            array($this, 'render_main_menu_page'),  // Callback function
            'dashicons-admin-generic', // Icon URL
            6                     // Position
        );

        // Add Job Offers submenu
        add_submenu_page(
            'job-management',                // Parent slug
            'Job Offers',                    // Page title
            'Job Offers',                    // Menu title
            'manage_options',     // Capability
            'edit.php?post_type=job-offers', // Submenu slug (link to custom post type)
            null  // No callback function needed, links to custom post type list
        );

        // Add Applicants submenu
        add_submenu_page(
            'job-management',                // Parent slug
            'Applicants',                    // Page title
            'Applicants',                    // Menu title
            'manage_options',               // Capability
            'edit.php?post_type=applicants', // Submenu slug (link to custom post type)
            null  // No callback function needed, links to custom post type list
        );
    }

    public function render_main_menu_page() {
        ?>
        <div class="wrap">
            <h1>Job Management</h1>
            <p>Welcome to the Job Management page.</p>
        </div>
        <?php
    }
}

$custom_admin_menu = new Custom_Admin_Menu();