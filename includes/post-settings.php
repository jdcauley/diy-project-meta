<?php 

  class DIY_Post_Meta extends DIY_Project_meta {

    function __construct () {

      add_action( 'add_meta_boxes', array( $this, 'meta_boxes') );

      add_action( 'save_post', array( $this, 'save_post_meta') );

      add_filter( 'the_content', array( $this, 'add_meta_to_content' ) );

      add_action( 'wp_enqueue_scripts', array( $this, 'public_styles') );

      add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ));

    }

    /* Public Post Styles */
    function public_styles () {

      wp_enqueue_style( self::PLUGIN_DOMAIN . '/diy-meta.css', self::assets_url() . '/dist/styles/diy-meta.css' );

    }

    /* Load Admin Styles for Post Editor */
    function admin_enqueue ( $hook ) {

      if ( $hook != 'post.php') {
        return;
      }

      wp_enqueue_style( self::PLUGIN_DOMAIN . '/post-editor.css', self::assets_url() . '/dist/styles/post-editor.css' );
      wp_register_script( self::PLUGIN_DOMAIN . '/post-editor.js', self::assets_url() . '/dist/scripts/post-editor.js', array( 'jquery' ) );
      wp_localize_script( self::PLUGIN_DOMAIN . '/post-editor.js', 'wpApiSettings', array(
        'root' => esc_url_raw( rest_url() ),
        'nonce' => wp_create_nonce( 'wp_rest' )
      ) );
      wp_enqueue_script( self::PLUGIN_DOMAIN . '/post-editor.js' );
    }

    /* Load Meta into Content */
    function add_meta_to_content ( $content ) {

      $options = get_option( self::PREFIX . 'settings' );

      $post_id = get_the_id();
      $time_key = '_' . self::PREFIX . 'time';
      $cost_key = '_' . self::PREFIX . 'cost';

      $post_terms = wp_get_post_terms( $post_id, self::PREFIX . 'difficulty' );

      $meta = $style = $difficulty = $time = $cost = '';

      $time = get_post_meta($post_id, $time_key, true);
      $cost = get_post_meta($post_id, $cost_key, true);

      if ( isset( $options['bg-color'] ) ) {
        $style = 'style="background-color: ' . $options['bg-color'] . ';"';
      }

      if ( count($post_terms) > 0 ) {
        $post_term = $post_terms[0];
        $difficulty = '<span class="diy-meta-entry diy-meta-difficulty ' . $post_Term->slug . '"><span class="diy-meta-title">Project Difficulty:</span> <span class="diy-meta-field">' . $post_term->name . '</span></span>';
      }

      if ( $time ) {
        $time = '<span class="diy-meta-entry diy-meta-time ' . $post_Term->slug . '"><span class="diy-meta-title">Estimated Time:</span> <span class="diy-meta-field">' . $time . '</span></span>';
      }

      if ( $cost ) {
        $cost = '<span class="diy-meta-entry diy-meta-time ' . $post_Term->slug . '"><span class="diy-meta-title">Estimated Cost:</span> <span class="diy-meta-field">$' . $cost . '</span></span>';
      }


      if ( $difficulty || $time || $cost ) {

        ob_start();
      ?>
        <div class="diy-meta" <?php echo $style; ?>>
          <?php echo $difficulty; ?><?php echo $time; ?><?php echo $cost; ?>
        </div>
      <?php
        $meta = ob_get_clean();
      }
      $content = $meta . $content;
      return $content;

    }

    /* Meta box for adding DIY Meta to Posts */
    function meta_boxes () {

      add_meta_box(
        self::PREFIX . 'difficulty', 
        __('DIY Project Details', self::TEXT_DOMAIN ), 
        array($this, 'difficulty_box'),
        'post'
      );

    }

    function difficulty_box ( $post ) {
      $terms = get_terms( array(
        'taxonomy' => self::PREFIX . 'difficulty',
        'hide_empty' => false,
        'orderby' => 'term_id', 
        'order' => 'ASC'
      ) );

      $value = '';
      $post_terms = wp_get_post_terms( $post->ID, self::PREFIX . 'difficulty' );
      if ( count( $post_terms ) > 0 ) {
        $value = $post_terms[0]->slug;
      }

      $time_key = '_' . self::PREFIX . 'time';
      $cost_key = '_' . self::PREFIX . 'cost';

      $time = get_post_meta($post->ID, $time_key, true);
      $cost = get_post_meta($post->ID, $cost_key, true);

      ?>
        <div class="diy-input-group">
          <label for="diy-project-meta-difficulty">How Hard is the Project?</label>
          <div class="diy-input-wrap">
            <select id="diy-project-meta-difficulty" class="" name="_<?php echo self::PREFIX; ?>difficulty" value="<?php echo $value; ?>">
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
            <p class="help-text">e.g. Option list can be edited in <a href="admin.php?page=<?php echo self::PLUGIN_DOMAIN; ?>"><?php echo __( 'settings', self::TEXT_DOMAIN ); ?></a>.</p>
          </div>
        </div>
        <div class="diy-input-group">
          <label for="diy-project-meta-length">How long will the Project Take?</label>
          <div class="diy-input-wrap">
            <input id="diy-project-meta-length" type="text" name="_<?php echo self::PREFIX; ?>time" class="regular-text diy-project-meta-length" value="<?php echo $time; ?>">
            <p class="help-text">e.g. 4 Hours, 2 Days, 15 minutes</p>
          </div>
        </div>
        <div class="diy-input-group">
          <label for="diy-project-meta-cost">How Much with the Project Cost?</label>
          <div class="diy-input-wrap diy-project-meta-cost">
            <input id="diy-project-meta-cost" type="text" name="_<?php echo self::PREFIX; ?>cost" class="regular-text" value="<?php echo $cost; ?>">
            <p class="help-text">e.g. 1,000, 500, 40.50</p>
          </div>
        </div>
      <?php

    }

    /* Logic for saving Meta to Post */
    function save_post_meta ( $post_id ) {

      if ( !current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
      }

      $difficulty_key = '_' . self::PREFIX . 'difficulty';
      $time_key = '_' . self::PREFIX . 'time';
      $cost_key = '_' . self::PREFIX . 'cost';

      if ( isset( $_POST[$difficulty_key] ) ) {
        $value = $_POST[$difficulty_key];
        wp_set_object_terms( $post_id, $value, self::PREFIX . 'difficulty');
      }

      if ( isset( $_POST[$time_key]) ) {
        $time = sanitize_text_field($_POST[$time_key]);
        update_post_meta( $post_id, $time_key, $time);
      }

      if ( isset( $_POST[$cost_key] ) ) {
        $cost = sanitize_text_field($_POST[$cost_key]);
        update_post_meta( $post_id, $cost_key, $cost);
      }
      
    }
  }