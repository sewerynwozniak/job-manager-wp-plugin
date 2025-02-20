<?php


class Job_Manager_Role {
    public function __construct() {
        add_action('init', array($this, 'add_custom_roles'));
        add_action('admin_init', array($this, 'add_custom_capabilities'));
    }

    public function add_custom_roles() {
        if (get_role('job_manager')) {
            remove_role('job_manager'); // Remove the role if it already exists
        }

        add_role('job_manager', 'Job Manager', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'manage_job_management' => true,
        ));
    }

    public function add_custom_capabilities() {
        $roles = array('job_manager', 'administrator');

        foreach ($roles as $role) {
            $role_object = get_role($role);

            if ($role_object) {
                $role_object->add_cap('read');
                $role_object->add_cap('manage_job_management');
                $role_object->add_cap('edit_job_offer');
                $role_object->add_cap('read_job_offer');
                $role_object->add_cap('delete_job_offer');
                $role_object->add_cap('edit_job_offers');
                $role_object->add_cap('edit_others_job_offers');
                $role_object->add_cap('publish_job_offers');
                $role_object->add_cap('read_private_job_offers');

                $role_object->add_cap('edit_applicant');
                $role_object->add_cap('read_applicant');
                $role_object->add_cap('delete_applicant');
                $role_object->add_cap('edit_applicants');
                $role_object->add_cap('edit_others_applicants');
                $role_object->add_cap('publish_applicants');
                $role_object->add_cap('read_private_applicants');
            }
        }
    }
}

new Job_Manager_Role();
