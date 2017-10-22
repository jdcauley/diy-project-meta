<?php 

  class DIY_Post_Meta extends DIY_Project_meta {

    function __construct () {

      add_action( 'add_meta_boxes', array( $this, 'meta_boxes') );

      add_action( 'save_post', array( $this, 'save_post_meta') );

    }

    function meta_boxes () {

      add_meta_box(
        self::PREFIX . 'difficulty', 
        __('DIY Project Details', 
        self::TEXT_DOMAIN ), 
        array($this, 'difficulty_box'),
        'post'
      );

    }

    function difficulty_box ( $post ) {
      $terms = get_terms( array(
        'taxonomy' => self::PREFIX . 'difficulty',
        'hide_empty' => false
      ) );

      $value = '';
      $post_terms = wp_get_post_terms( $post->ID, self::PREFIX . 'difficulty' );
      if ( count( $post_terms ) > 0 ) {
        $value = $post_terms[0]->slug;
      }

      ?>
        <div class="input-group">
          <label>How Hard is the Project?</label>
          <select class="" name="_<?php echo self::PREFIX; ?>difficulty" value="<?php echo $value; ?>">
            <option></option>
            <?php foreach($terms as $term) { 
              $selected = '';
              if ( $term->slug == $value) {
                $selected = 'selected';
              }
            ?>
              <option value="<?php echo $term->slug; ?>" <?php echo $selected; ?>>
                <?php echo $term->name; ?>
              </option>
            <?php } ?>
          </select>
        </div>
        <div class="input-group">
          <label>How long will the Project Take?<label>
          <input type="text">
        </div>
        <div class="input-group">
          <label>How Much with the Project Cost?<label>
          <input type="text">
        </div>
      <?php

    }

    function save_post_meta ( $post_id ) {

      if ( !current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
      }
      $key = '_' . self::PREFIX . 'difficulty';

      if ( !isset( $_POST[$key] ) ) {
        return $post_id;
      }
      $value = $_POST[$key];
      wp_set_object_terms( $post_id, $value, self::PREFIX . 'difficulty');
      
    }
  }