<?php

class Job_Offer_Template {


    public function __construct() {

    add_action( 'single_template', array( $this, 'load_template') );
    
    }

    public function load_template( $template ) {
        global $post;
        
        if ( 'job-offers' === $post->post_type ) {
            return plugin_dir_path( __DIR__ ) . 'templates/single-job-offer.php';
        }
    
        return $template;
    }



}



$job_offer_template = new Job_Offer_Template();
