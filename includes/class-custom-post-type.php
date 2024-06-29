<?php

class Custom_Post_Type {

    public function __construct() {
        add_action( 'init', array( $this, 'register_job_offer' ) );
        add_action( 'init', array( $this, 'register_applicants' ) );
    }

    public function register_job_offer() {
        register_post_type( 'job-offers',
            array(
                'labels' => array(
                    'name' => __( 'Job Offers' ),
                    'singular_name' => __( 'Job Offer' )
                ),
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'job-offers'),
                'show_in_rest' => true,
                'supports' => array('title', 'editor', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
                'show_in_menu' => false
                
     
            )
        );
    }

    public function register_applicants() {
        register_post_type( 'applicants',
            array(
                'labels' => array(
                    'name' => __( 'Applicants' ),
                    'singular_name' => __( 'Applicant' )
                ),
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'applicants'),
                'show_in_rest' => true,
                'supports' => array('title', 'editor', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
                'show_in_menu' => false
            
       
            )
        );
    }
}

$custom_post_type = new Custom_Post_Type();




