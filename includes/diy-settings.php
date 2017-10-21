<?php

 class DIY_Settings extends DIY_Project_Meta {

    function __construct () {

      add_action( 'admin_init', array( $this, 'settings_page' ) );

      add_action( 'add_meta_boxes', array( $this, 'meta_boxes') );

      add_action( 'admin_menu', array( $this, 'update_admin_menu' ) );

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

        $options = get_option( self::PREFIX . 'settings' );
        $name = self::PREFIX . 'settings' . '[test_field]';
      
      ?>
        <h4>Create Your Difficulty Options</h4>
        <span>Add as many options for difficulty settings as you would like, these will be displayed in your posts editor</span>

        <div>
          <input type="text" name="<?php echo $name; ?>" value="<?php echo $options['test_field']; ?>">
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

    function difficulty_box ( $post ) {
      ?>
        <select class="" name="diy_project_difficulty">
          <option value="1">Easy</option>
          <option value="2">Pretty Easy</option>
          <option value="3">Kind of Hard</option>
          <option value="4">Difficult</option>
          <option value="5">Very Difficult</option>
        </select>
      <?php
    }

    function meta_boxes () {

      add_meta_box( self::PREFIX . 'difficulty', __('Project Difficulty', self::TEXT_DOMAIN ), array($this, 'difficulty_box'), 'post' );

    }

 }