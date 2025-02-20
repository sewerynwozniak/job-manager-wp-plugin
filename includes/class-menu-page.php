<?php

if (!defined('ABSPATH')) {
    exit;
}

class Custom_Admin_Menu {
    public function __construct() {
        add_action('admin_menu', array($this, 'create_admin_menu'));
        
    }

    public function create_admin_menu() {
        add_menu_page(
            'Job Management',
            'Jobs',
            'manage_job_management',
            'job-management',
            array($this, 'render_main_menu_page'),
            'dashicons-admin-generic',
            30
        );

        add_submenu_page(
            'job-management',
            'Job Offers',
            'Job Offers',
            'manage_job_management',
            'edit.php?post_type=job-offers',
            null
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

// ✅ Initialize Class
new Custom_Admin_Menu();
