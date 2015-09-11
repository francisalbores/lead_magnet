<?php
// ADDING META BOX FOR POST EDIT SCREEN
function lead_magnet_add_meta_box() {
  $screens = array( 'post' );
  foreach ( $screens as $screen ) {
    add_meta_box(
      'lead_magnet_sectionid',
      __( 'Lead Magnet', 'init' ),
      'lead_magnet_meta_box_callback',
      $screen
    );
  }
}
add_action( 'add_meta_boxes', 'lead_magnet_add_meta_box' );
function lead_magnet_meta_box_callback( $post ) {
  wp_nonce_field( basename( __FILE__ ), 'lead_magnet_meta_box_nonce' );
  ?>
  <p>
    <label for="url-suffix-post-class"><?php _e( "Choose a Lead Magnet to associate for this post.", 'init' ); ?></label>
    <br />
    <select class="widefat" name="lead-magnet-class">
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
        ?>
        <option value="<?php echo $p ?>"><?php echo $name ?></option>
        <?php
      }
      ?>
    </select>
  </p>
  <?php
}