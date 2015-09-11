<?php
/*
Plugin Name: Wishloop Blog Segmentation Plugin
Plugin URI:  http://wishloop.com
Description: A simple plugin which will allow us to show targeted Engagifire popups to readers of our blog based on the topics they are viewing.
Version:     1.0
Author:      Francis Albores
Author URI:  fanboom.net/francis-albores/
*/

include 'posts.php';

add_action( 'load-post.php', 'url_suffix_setup' );
add_action( 'load-post-new.php', 'url_suffix_setup' );

// Add custom post type:
add_action( 'init', 'create_post_type' );
function create_post_type() {
  register_post_type( 'leadMagnet',
    array(
      'labels' => array(
        'name' => __( 'Lead Magnets' ),
        'add_new_item'=> __('Add New Lead Magnet'),
        'singular_name' => __( 'Lead Magnet' )
      ),
      'public' => true,
      'has_archive' => true,
      'rewrite' => array('slug' => 'movies'),
      'supports' => array('title')
    )
  );
}
// Add Meta Box to Custom Post Type
function url_suffix_setup() {
  /* Add meta boxes on the 'add_meta_boxes' hook. */
  add_action( 'add_meta_boxes', 'url_suffix_meta_boxes' );
  add_action( 'save_post', 'url_suffix_save', 10, 2 );
}
function url_suffix_meta_boxes() {
  add_meta_box(
    'url-suffix-class',      // Unique ID
    esc_html__( 'URL Suffix', 'init' ),    // Title
    'url_suffix_class_meta_box',   // Callback function
    'leadMagnet',         // Admin page (or post type)
    'advanced',         // Context
    'high'         // Priority
  );
}
function url_suffix_class_meta_box( $object, $box ) { ?>
  <?php wp_nonce_field( basename( __FILE__ ), 'url_suffix_class_nonce' ); ?>
  <p>
    <label for="url-suffix-post-class"><?php _e( "These post URL suffixes will then be used in Engagifire as the basis of the popup targeting and will determine which popups show on which posts.", 'init' ); ?></label>
    <br />
    <input class="widefat" type="text" name="url-suffix-class" id="url-suffix-class" value="<?php echo esc_attr( get_post_meta( $object->ID, 'url-suffix-class', true ) ); ?>" size="30" />
  </p>
<?php }
function url_suffix_save($post_id, $post ) {
  if ( !isset( $_POST['url_suffix_class_nonce'] ) || !wp_verify_nonce( $_POST['url_suffix_class_nonce'], basename( __FILE__ ) ) )
    return $post_id;
  $post_type = get_post_type_object( $post->post_type );
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;
  $new_meta_value = ( isset( $_POST['url-suffix-class'] ) ? sanitize_html_class( $_POST['url-suffix-class'] ) : '' );
  $meta_key = 'url-suffix-class';
  $meta_value = get_post_meta( $post_id, $meta_key, true );
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}


