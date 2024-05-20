<?php

class Custom_Post_Type{

    public function __construct() {
        add_action( 'init', array( $this, 'register_custom_post_type' ) );
    }
    
    public function register_custom_post_type() {
        register_post_type( 'job-offer',
            array(
                'labels' => array(
                    'name' => __( 'Job Offer' ),
                    'singular_name' => __( 'Job Offers' )
                ),
                'public' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => 'job-offer'),
                'show_in_rest' => true,
                'supports'  => array('title', 'editor', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields')
            )
        );
    }

}

$offer = new Custom_Post_Type();