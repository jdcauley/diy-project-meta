<?php

  class DIY_Settings extends DIY_Project_Meta {

    function __construct () {

      add_action( 'admin_init', array( $this, 'settings_page' ) );

      add_action( 'admin_menu', array( $this, 'update_admin_menu' ) );

      add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ));

      add_filter( 'update_option', array( $this, 'options_saved') );

    }

    /* Enqueue Scripts for Load Scripts and Styles for DIY Meta Project */
    function admin_enqueue ( $hook ) {

      if ( $hook != 'toplevel_page_diy_meta_plugin' ) {
        return;
      }

      wp_enqueue_style( self::PLUGIN_DOMAIN . '/settings.css', self::assets_url() . '/dist/styles/settings.css' );

      wp_enqueue_style( 'wp-color-picker' );

      wp_register_script( self::PLUGIN_DOMAIN . '/settings.js', self::assets_url() . '/dist/scripts/settings.js', array( 'wp-color-picker' ) );
      wp_localize_script( self::PLUGIN_DOMAIN . '/settings.js', 'wpApiSettings', array(
        'root' => esc_url_raw( rest_url() ),
        'nonce' => wp_create_nonce( 'wp_rest' )
      ) );
      wp_enqueue_script( self::PLUGIN_DOMAIN . '/settings.js' );

    }

    /* Save Term if no JS */
    function options_saved ( $data ) {

      if ( isset( $_POST['diy_meta_new_term'] ) ) {
        $term_name = sanitize_text_field( $_POST['diy_meta_new_term'] );
        wp_insert_term( $term_name, self::PREFIX . 'difficulty', array() );
      }

    }

    /* Render Options Page */
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

    /* Add Option Menu and to Dashboard */
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

    /* Setting Page Markup */
    function settings_page_title ( $arg ) {
      ?>
        <h1><?php __( 'DIY Meta Settings', self::TEXT_DOMAIN ); ?></h1>
      <?php
    }

    /* Difficulty Settings Markup */
    function difficulty_settings ( $arg ) {

      $options = get_option( self::PREFIX . 'settings' );

      $terms = get_terms( array(
        'taxonomy' => self::PREFIX . 'difficulty',
        'hide_empty' => false,
        'orderby' => 'term_id', 
        'order' => 'ASC'
      ) );

      ?>
        <p class="help-text">Add custom difficulty options to select and display in posts</p>
        <div id="term-notices" class="notifications"></div>
        
        <ul id="difficulty-term-list" class="difficulty-term-list">
        <?php foreach ( $terms as $term ) { ?>
          <li class="difficulty-term" data-term-id="<?php echo $term->term_id; ?>">
            <h3><?php echo $term->name; ?></h3>
            <a href="#" class="term-delete" data-term-id="<?php echo $term->term_id; ?>">Delete</a>
          </li>
        <?php } ?>
        </ul>

        <div class="difficulty-term-create">
          <input id="<?php echo self::PREFIX . 'new_term'; ?>" class="regular-text term-create-input" name="<?php echo self::PREFIX . 'new_term'; ?>" value="" placeholder="new term">
          <button id="<?php echo self::PREFIX . 'new_term-button'; ?>" class="button button-primary" type="submit">Add</button>
        </div>

      <?php

    }

    /* Background Color Field and Markup */
    function background_color () {

      $options = get_option( self::PREFIX . 'settings' );
      $field_name = self::PREFIX . 'settings[bg-color]';
      $value = '';
      if ( isset($options['bg-color']) ) {
        $value = sanitize_text_field($options['bg-color']);
      }
    ?>
        <input type="text" name="<?php echo $field_name; ?>" class="bg-color-picker" value="<?php echo $value; ?>">
    <?php
    }

    function settings_page () {

      register_setting(
        self::PLUGIN_DOMAIN,
        self::PREFIX . 'settings'
      );

      /* Create Setting Section */
      add_settings_section(
        self::PREFIX . 'settings_difficulty_options',
        __( 'DIY Meta Settings', self::TEXT_DOMAIN),
        array( $this, 'settings_page_title'),
        self::PLUGIN_DOMAIN
      );

      /* Background Color Setting */
      add_settings_field(
        self::PREFIX . 'display_background',
        __( 'Background Color', self::TEXT_DOMAIN ),
        array( $this, 'background_color' ),
        self::PLUGIN_DOMAIN,
        self::PREFIX . 'settings_difficulty_options'
      );

      /* Form and Mark for Custom Difficulties */
      add_settings_field(
        self::PREFIX . 'difficulty_settings',
        __( 'Difficulty Level', self::TEXT_DOMAIN ),
        array( $this, 'difficulty_settings' ),
        self::PLUGIN_DOMAIN,
        self::PREFIX . 'settings_difficulty_options'
      );

    }

  }