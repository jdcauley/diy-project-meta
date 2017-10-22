<?php

 class DIY_Settings extends DIY_Project_Meta {

    function __construct () {

      add_action( 'admin_init', array( $this, 'settings_page' ) );

      add_action( 'add_meta_boxes', array( $this, 'meta_boxes') );

      add_action( 'admin_menu', array( $this, 'update_admin_menu' ) );

      add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ));

      add_filter( 'update_option', array( $this, 'options_saved') );

      add_action( 'save_post', array( $this, 'save_post_meta') );

    }

    function options_saved ( $data ) {

      if ( isset( $_POST['diy_meta_new_term'] ) ) {
        $term_name = sanitize_text_field( $_POST['diy_meta_new_term'] );
        wp_insert_term( $term_name, self::PREFIX . 'difficulty', array() );
      }

    }

    function admin_enqueue ( $hook ) {

      if ( $hook != 'toplevel_page_diy_meta_plugin' ) {
        return;
      }

      wp_register_script( self::PLUGIN_DOMAIN . '/settings.js', self::assets_url() . '/dist/scripts/settings.js' );
      wp_enqueue_script( self::PLUGIN_DOMAIN . '/settings.js' );

    }

    function options_page () {

      ?>
        <form action='options.php' method='post'>

          <?php
            settings_fields( self::PLUGIN_DOMAIN );
            do_settings_sections( self::PLUGIN_DOMAIN );
            submit_button();
          ?>

        </form>
      <?php

    }
   
    function update_admin_menu () {

      add_menu_page(
        __( 'DIY Project Settings', self::TEXT_DOMAIN ),
        __( 'DIY Settings', self::TEXT_DOMAIN ), 
        'manage_options', 
        self::PLUGIN_DOMAIN, 
        array( $this, 'options_page'),
        'dashicons-clock'
      );

    }

    function settings_page_title ( $arg ) {
      ?>
        <h1><?php __( 'DIY Meta Settings', self::TEXT_DOMAIN ); ?></h1>
      <?php
    }

    function difficulty_settings ( $arg ) {

      $terms = get_terms( array(
        'taxonomy' => self::PREFIX . 'difficulty',
        'hide_empty' => false
      ) );

      // print_r( $terms );

      ?>
        <h3>Difficulty Options</h3>
        <ul>
      <?php

      foreach ( $terms as $term ) {
        ?>
        <li>
          <h4><?php echo $term->name; ?></h4>
        </li>
        <?php
      }

      ?>
    </ul>
        <div>
          <input class="regular-text" name="<?php echo self::PREFIX . 'new_term'; ?>" value="" placeholder="new term">
        </div>
      <?php

    }

    function settings_page () {

      register_setting(
        self::PLUGIN_DOMAIN,
        self::PREFIX . 'settings'
      );

      add_settings_section(
        self::PREFIX . 'settings_difficulty_options',
        __( 'DIY Meta Settings', self::TEXT_DOMAIN),
        array( $this, 'settings_page_title'),
        self::PLUGIN_DOMAIN
      );
      
      add_settings_field(
        self::PREFIX . 'test_field',
        __( 'Difficulty Options', self::TEXT_DOMAIN ),
        array( $this, 'difficulty_settings' ),
        self::PLUGIN_DOMAIN,
        self::PREFIX . 'settings_difficulty_options'
      );

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
        <label>Project Difficulty</label>
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