<?php
// ADDING META BOX FOR POST EDIT SCREEN
function lead_magnet_add_meta_box() {
  $screens = array( 'post' );
  foreach ( $screens as $screen ) {
    add_meta_box(
      'lead-magnet-class',
      __( 'Lead Magnet', 'init' ),
      'lead_magnet_meta_box_callback',
      $screen
    );
  }
}
function lead_magnet_meta_box_callback( $object ) {
  wp_nonce_field( basename( __FILE__ ), 'lead_magnet_meta_box_nonce' );
  ?>
  <?php $lmvalue = esc_attr( get_post_meta( $object->ID, 'lead-magnet-class', true ) ); ?>
  <p>
    <label for="url-suffix-post-class"><?php _e( "Choose a Lead Magnet to associate for this post.", 'init' ); ?></label>
    <br />
    <select class="widefat" name="lead-magnet-class" id="lead-magnet-class">
      <?php
      $posts = get_posts(array(
        'post_type'   => 'leadMagnet',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids'
        )
      );
      foreach($posts as $p){
        $name = get_the_title($p);
        $suffix = get_post_meta($p, 'url-suffix-class', true);
        if($lmvalue == $p) {
          ?>
          <option data-id="<?php echo $p ?>" selected value="<?php echo $suffix ?>"><?php echo $name ?></option>
          <?php
        }
        else{
          ?>
          <option data-id="<?php echo $p ?>" value="<?php echo $suffix ?>"><?php echo $name ?></option>
          <?php
        }
      }
      ?>
    </select>
  </p>
  <script>
  jQuery("#publish").on('click',function(){
    //console.log('test'); return false;
  })
  </script>
  <?php
}
function lead_magnet_suffix_save($post_id, $post ) {
  if ( !isset( $_POST['lead_magnet_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['lead_magnet_meta_box_nonce'], basename( __FILE__ ) ) )
    return $post_id;
  $post_type = get_post_type_object( $post->post_type );
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;
  $new_meta_value = ( isset( $_POST['lead-magnet-class'] ) ? sanitize_html_class( $_POST['lead-magnet-class'] ) : '' );
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}

// Updating Post Slug
add_filter( 'wp_insert_post_data', 'my_function', 50, 2 );
function my_function( $data, $postarr ) {
  $meta_key = 'lead-magnet-class';
  $meta_value = get_post_meta( $postarr['post_ID'], $meta_key, true );
  if ( !in_array( $data['post_status'], array( 'draft', 'pending', 'auto-draft' ) ) ) {
      $data['post_name'] = sanitize_title( $data['post_title'] ).'-'.$meta_value;
  }
  return $data;
}