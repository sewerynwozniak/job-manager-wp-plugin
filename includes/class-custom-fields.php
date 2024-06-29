<?php

class Custom_Fields_Manager {

private $post_type;
private $fields;

public function __construct($post_type, $fields) {
    $this->post_type = $post_type;
    $this->fields = $fields;

    add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
    add_action( 'save_post', array( $this, 'save_meta_box_data' ) );
}


public function add_meta_box() {
    add_meta_box(
        "{$this->post_type}_meta_box",
        ucfirst($this->post_type) . ' Details',
        array( $this, 'render_meta_box' ),
        $this->post_type,
        'normal',
        'high'
    );
}

public function render_meta_box( $post ) {
    // Output the nonce field for security
    wp_nonce_field( "{$this->post_type}_meta_box", "{$this->post_type}_meta_nonce" );

    foreach ( $this->fields as $field ) {
        $value = get_post_meta( $post->ID, $field, true );
        ?>
        <p>
            <label for="<?php echo esc_attr( $field ); ?>"><?php echo ucfirst( $field ); ?>:</label><br>
            <input type="text" id="<?php echo esc_attr( $field ); ?>" name="<?php echo esc_attr( $field ); ?>" value="<?php echo esc_attr( $value ); ?>">
        </p>
        <?php
    }
}

public function save_meta_box_data( $post_id ) {
    // Check if our nonce is set.
    if ( ! isset( $_POST["{$this->post_type}_meta_nonce"] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST["{$this->post_type}_meta_nonce"], "{$this->post_type}_meta_box" ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Sanitize and save the meta data
    foreach ( $this->fields as $field ) {
        if ( isset( $_POST[$field] ) ) {
            update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
        }
    }
}
}


$post_type = 'job-offers';
$fields = array( 'place', 'salary' );

$custom_fields_manager = new Custom_Fields_Manager( $post_type, $fields );
