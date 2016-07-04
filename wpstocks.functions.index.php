<?php

function wpstocks_admin_action_javascript()
{
    if ( WPSTOCKS_USE_BOOTSTRAP ) {
        wp_deregister_style( 'bootstrap-css' );
        wp_register_style( 'bootstrap-css', plugin_dir_url( __FILE__ ) . 'styles/bootstrap.wordpress.css' );
        wp_enqueue_style( 'bootstrap-css' );
        wp_deregister_script( 'bootstrap-collapse' );
        wp_register_script( 'bootstrap-collapse', plugin_dir_url( __FILE__ ) . 'js/bootstrap/collapse.js' );
        wp_enqueue_script( 'bootstrap-collapse' );
        wp_deregister_script( 'bootstrap' );
        wp_register_script( 'bootstrap', plugin_dir_url( __FILE__ ) . 'js/bootstrap.min.js' );
        wp_enqueue_script( 'bootstrap' );
    }
}

function wpstocks_action_javascript()
{

    // Styles for non-admin pages
    wp_deregister_style( 'font-awesome-css' );
    wp_register_style( 'font-awesome-css', plugin_dir_url( __FILE__ ) . 'styles/font-awesome.min.css' );
    wp_enqueue_style( 'font-awesome-css' );

    wp_deregister_style( 'weather-icon-css' );
    wp_register_style( 'weather-icon-css', plugin_dir_url( __FILE__ ) . 'styles/weather-icons.min.css' );
    wp_enqueue_style( 'weather-icon-css' );

    if ( WPSTOCKS_USE_BOOTSTRAP ) {
        wp_deregister_style( 'bootstrap-css' );
        wp_register_style( 'bootstrap-css', plugin_dir_url( __FILE__ ) . 'styles/bootstrap.wordpress.css' );
        wp_enqueue_style( 'bootstrap-css' );
    }

    // Javascript for non-admin pages
    wp_deregister_script( 'modernizr' );
    wp_register_script( 'modernizr', plugin_dir_url( __FILE__ ) . 'js/vendor/modernizr-2.6.2.min.js' );
    wp_enqueue_script( 'modernizr' );

}


function wpstocks_template(  $template  )
{
    global $post;
    
    // Get the user we are acting on behalf of - this will passed as a "GET" parameter
    @session_start();
    if (  !isset(  $_GET['userId']  )  ) {
        unset(  $_SESSION['wpstocks_user']  );
    } else {
        $_SESSION['wpstocks_user'] = new \premiumwebtechnologies\wpstocks\WPStocksUser(  $_GET['userId']  );
    }

    if (  $post->post_type == 'wpstocks_page'  ) {
        return plugin_dir_path(  __FILE__  ) . "wpstocks.page.html.php";
    }
    return $template;
}

function wpstocks_plugins_loaded()
{
    require_once( ABSPATH . WPINC . '/pluggable.php' );
    $current_user = wp_get_current_user();
    $userID = $current_user->ID;

    if ( is_user_logged_in() ) {
        $user_info = get_userdata( get_current_user_id() );
    }

    add_action( 'wp_head', 'wpstocks_action_javascript' );
    add_action( 'wp_footer', 'wpstocks_action_javascript_footer' );

    wp_enqueue_script( "jquery" );
    wp_enqueue_style( "pwt_style", plugin_dir_url( __FILE__ ) . "styles/pwt.css" );
    wp_enqueue_style( "wpstocks_style", plugin_dir_url( __FILE__ ) . "styles/wpstocks.css" );
    wp_enqueue_script( "wpstocks_gsdom", plugin_dir_url( __FILE__ ) . "js/gsdom.js" );
    wp_enqueue_script( "wpstocks_messaging", plugin_dir_url( __FILE__ ) . "js/messaging.js" );
    //    wp_enqueue_script( "googlemaps_js", "https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true" );
    wp_enqueue_script( "wpstocks_js", plugin_dir_url( __FILE__ ) . "/js/wpstocks.js" );

    $params = array( 
        'ajax_url' => admin_url() . 'admin-ajax.php',
        'p1' => 1,
        'p2' => 2,
        'p3' => 3 );
    wp_localize_script( 'wpstocks_js', 'wpstocks_js_parameters', $params );

    add_action( 'admin_enqueue_scripts', 'wpstocks_admin_action_javascript' );

    add_action( 'admin_menu', 'wpstocks_admin' );

    if ( function_exists( "wpstocks_add_contact_methods" ) ) {
        add_filter( 'user_contactmethods', 'wpstocks_add_contact_methods' );
    }

    if ( function_exists( "wpstocks_show_user_profile" ) ) {
        add_action( 'show_user_profile', 'wpstocks_show_user_profile' );
        add_action( 'edit_user_profile', 'wpstocks_show_user_profile' );
    }

    if ( function_exists( "wpstocks_update_user_profile" ) ) {
        add_action( 'personal_options_update', "wpstocks_update_user_profile" );
        add_action( 'edit_user_profile_update', "wpstocks_update_user_profile" );
    }

    add_action( 'admin_init', 'wpstocks_admin_init' );


    add_action( 'wp_ajax_nopriv_wpstocks_ajaxRegister', 'wpstocks_ajaxRegister' );
    add_action( 'wp_ajax_wpstocks_ajaxRegister', 'wpstocks_ajaxRegister' );

    add_action( 'wp_ajax_nopriv_wpstocks_ajax_getAccounts', 'wpstocks_ajax_getAccounts' );
    add_action( 'wp_ajax_wpstocks_ajax_getAccounts', 'wpstocks_ajax_getAccounts' );

    add_action( 'wp_ajax_nopriv_wpstocks_ajax_createAccount', 'wpstocks_ajax_createAccount' );
    add_action( 'wp_ajax_wpstocks_ajax_createAccount', 'wpstocks_ajax_createAccount' );

    add_action( 'wp_ajax_nopriv_wpstocks_ajax_getClientDetails', 'wpstocks_ajax_getClientDetails' );
    add_action( 'wp_ajax_wpstocks_ajax_getClientDetails', 'wpstocks_ajax_getClientDetails' );

    add_action( 'wp_ajax_nopriv_wpstocks_ajax_getAccountDetails', 'wpstocks_ajax_getAccountDetails' );
    add_action( 'wp_ajax_wpstocks_ajax_getAccountDetails', 'wpstocks_ajax_getAccountDetails' );

    add_action( 'wp_ajax_nopriv_wpstocks_ajax_makeDeposit', 'wpstocks_ajax_makeDeposit' );
    add_action( 'wp_ajax_wpstocks_ajax_makeDeposit', 'wpstocks_ajax_makeDeposit' );

    add_action( 'wp_ajax_nopriv_wpstocks_ajax_makeWithdraw', 'wpstocks_ajax_makeWithdraw' );
    add_action( 'wp_ajax_wpstocks_ajax_makeWithdraw', 'wpstocks_ajax_makeWithdraw' );

    add_action( 'wp_ajax_nopriv_wpstocks_ajax_getStock', 'wpstocks_ajax_getStock' );
    add_action( 'wp_ajax_wpstocks_ajax_getStock', 'wpstocks_ajax_getStock' );

    add_action( 'wp_ajax_nopriv_wpstocks_ajax_openPosition', 'wpstocks_ajax_openPosition' );
    add_action( 'wp_ajax_wpstocks_ajax_openPosition', 'wpstocks_ajax_openPosition' );

    add_action( 'wp_ajax_nopriv_wpstocks_ajax_getPortfolio', 'wpstocks_ajax_getPortfolio' );
    add_action( 'wp_ajax_wpstocks_ajax_getPortfolio', 'wpstocks_ajax_getPortfolio' );

    add_action( 'wp_ajax_nopriv_wpstocks_ajax_getTransactions', 'wpstocks_ajax_getTransactions' );
    add_action( 'wp_ajax_wpstocks_ajax_getTransactions', 'wpstocks_ajax_getTransactions' );
    
    // Notifications
    // http://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
    if ( !isset( $_GET['page'] ) || ( $_GET['page'] != 'wpstocks_menu_setup' ) ) {
        $wpstocks = get_option( 'wpstocks', array() );
        if ( !isset( $wpstocks['visited pages'] ) || !in_array( 'admin', $wpstocks['visited pages'] ) ) {
            //  add_action(  'admin_notices', 'wpstocks_getting_started'  );
        }
    }

    // pro notification
    if ( !isset( $_GET['page'] ) || ( $_GET['page'] != 'wpstocks_promo_page' ) ) {
        $wpstocks = get_option( 'wpstocks', array() );
        if ( !isset( $wpstocks['visited pages'] ) || in_array( 'pro', $wpstocks['visited pages'] ) ) {
            //      add_action(  'admin_notices', 'wpstocks_get_pro_version'  );
        }

    }


    //    add_action( 'add_meta_boxes', 'wpstocks_add_meta_box'  );
    //    add_action( 'save_post', 'kasik_save_meta' );

    //    add_shortcode(  'SC', 'wpstocks_shortcode'  );
    wpstocks_setWPStocksURLs();
    add_filter( 'login_redirect', 'wpstocks_loginRedirect', 10, 3 );

    // Metaboxes
    add_action(  'load-post.php', 'wpstocks_metaboxes_setup'  );
    add_action(  'load-post-new.php', 'wpstocks_metaboxes_setup'  );
 //   add_action(  'load-user-new.php', 'wpstocks_user_metaboxes_setup'  );
    add_action(  'edit_user_profile', 'custom_user_profile_fields'  );
    add_action(  "user_new_form", "custom_user_profile_fields"  );
    add_action( 'user_register', 'save_custom_user_profile_fields' );
    add_action( 'edit_user_profile_update', 'save_custom_user_profile_fields' );

}

function custom_user_profile_fields( $user ){
    $userPage = new \premiumwebtechnologies\wpstocks\WPStocksUserPage( $user );
    $userPage->renderFields();
}


function save_custom_user_profile_fields( $user_id ){
    $user = get_user_by( 'ID', $user_id );
    if (  $user==false  ) {
        die( 'Could not get user' );
    }
    $userPage = new \premiumwebtechnologies\wpstocks\WPStocksUserPage( $user );
    $userPage->saveFields();
}

function wpstocks_metaboxes_setup()
{
    add_action(  'add_meta_boxes', function(){
        $WPStocksAccounts = new \premiumwebtechnologies\wpstocks\WPStocksAccounts();
        $WPStocksAccounts->add_meta_form();
    } );
}

function wpstocks_getControlPanelURL()
{
    $wpstocks_URLs = get_option( 'wpstocks_URLs' );
    return $wpstocks_URLs['control panel'];
}

function wpstocks_loginRedirect( $redirect_to, $request, $user )
{
    //is there a user to check?
    global $user;
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        //check for admins
        if ( in_array( 'wpstocks', $user->roles ) ) {
            // redirect to dashboard
            // http://localhost:8888/wp/?post_type=wpstocks_page&p=2241
            return wpstocks_getControlPanelURL();
        } else {
            return $redirect_to;
        }
    } else {
        return $redirect_to;
    }
}


function wpstocks_get_pro_version()
{
    // updated error update-nag
    ?>
    <div class="update-nag pwt_notification">
        <p><?php _e( 'Sign up for WP Stocks Pro and get 50% off the price! <a href="' . admin_url() . 'admin.php?page=wpstocks_promo_page">Limited offer. Click here now!</a>' ); ?></p>
        <p><a target="_NEW" href="http://premiumwebtechnologies.com"> Visit PremiumWebTechnologies.com for more
                hassle-free Wordpress plugins</a></p>
    </div>
    <?php
}

function wpstocks_getting_started()
{
    // updated error update-nag
    ?>
    <div class="update-nag pwt_notification">
        <p><?php _e( 'With YT Contextual plugin activated, you can now add contextual YouTube videos to your pages and posts! <a href="' . admin_url() . 'admin.php?page=ytc_plugin_settings">To get started, click here.</a>' ); ?></p>
        <p><a target="_NEW" href="http://example.com">[Plugin business name] for Hassle-free Wordpress plugins</a></p>
    </div>
    <?php
}


function wpstocks_admin_notice()
{
    // updated error update-nag
    ?>
    <div class="update-nag">
        <p><?php _e( 'Updated!', 'my-text-domain' ); ?></p>
    </div>
    <?php
}

function wpstocks_admin_init()
{
}

function wpstocks_init()
{
    wpstocks_add_page_type();
    //      wpstocks_createPages()
    $WPStocksAccounts = new \premiumwebtechnologies\wpstocks\WPStocksAccounts();
    $WPStocksAccounts->add_accounts_page_type();
}


/**
 * Add the fb-root div tag required for facebook
 *
 * @return null
 */
function wpstocks_action_javascript_footer()
{
}

function wpstocks_admin()
{
    if ( current_user_can( 'manage_options' ) ) {

        // add_menu_page(  $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position  );
        //      $about_page = add_menu_page( 'WP Wpstocks', "About WP Stocks", 'publish_posts', 'wpstocks_menu_setup', "wpstocks_about", plugin_dir_url( __FILE__ ).'images/logo20x20.png', '65.110' );
        //    $settings_page = add_submenu_page( 'wpstocks_menu_setup', 'WP Stocks Settings', "WP Stocks Settings", 'publish_posts', "wpstocks_settings_page", "wpstocks_settings_page"  );
        //    $admin_page = add_menu_page( 'WP Stocks Admin', "WP Stocks Admin", 'publish_posts', 'wpstocks_menu_setup', "wpstocks_admin_form", plugin_dir_url( __FILE__ ).'images/logo20x20.png', '65.120' );
        //    $promo_page = add_submenu_page( 'wpstocks_menu_setup', 'Get WP Stocks Pro', "Get Wpstocks Pro", 'publish_posts', "wpstocks_promo_page", "wpstocks_promo_page"  );

        /*
      $about_page = add_menu_page( 
      '',                     // No need to have this
      'MobilePop CTA',            // Menu Label
      'manage_options',
      'mpc_plugin_settings',   // ( * ) Shared slug
      'mpc_display',
      plugin_dir_url( __FILE__ ).'images/logo20x20.png',
      '65.100'

       );

      add_submenu_page( 
      'mpc_plugin_settings',   // ( * ) Shared slug
      'Display',   // Subpage Title
      'Display',             // Submenu Label
      'manage_options',
      'mpc_plugin_settings',   // ( * ) Shared slug
      'mpc_display'
       );

      add_submenu_page( 
      'mpc_plugin_settings',   // ( * ) Shared slug
      'Social',   // Subpage Title
      'Social',             // Submenu Label
      'manage_options',
      'social',   // ( * ) Shared slug
      'mpc_social'
       );

      add_submenu_page( 
      'mpc_plugin_settings',   // ( * ) Shared slug
      'Output',   // Subpage Title
      'Ouput',             // Submenu Label
      'manage_options',
      'output',   // ( * ) Shared slug
      'mpc_output'
       );

        */
        //      add_action(  'admin_footer-'. $about_page, 'wpstocks_admin_footer'  );
        //    add_action(  'admin_footer-'. $settings_page, 'wpstocks_admin_footer'  );
        //    add_action(  'admin_footer-'. $admin_page, 'wpstocks_admin_footer'  );
        //    add_action(  'admin_footer-'. $promo_page, 'wpstocks_admin_footer'  );


    }
}

function wpstocks_admin_footer()
{
    // http://api.jqueryui.com/
    ?>
    <style>
    </style>
    <?php
}

/*
  class wpstocks_button {

  function wpstocks_button()
  {
  if ( is_admin() ) {
  require_once( ABSPATH . WPINC . '/pluggable.php' );
  global $current_user;
  if (  current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) && get_user_option( 'rich_editing' )	 == 'true' ) {
  add_filter( 'tiny_mce_version', array( &$this, 'tiny_mce_version' )  );
  add_filter( "mce_external_plugins", array( &$this, "mce_external_plugins" ) );
  add_filter( 'mce_buttons_3', array( &$this, 'mce_buttons' ) );
  }
  }
  }

  function mce_buttons( $buttons )
  {
  array_push( $buttons, "separator", "wpgpPHP", "wpgpCSS", "wpgpHTML", "wpgpJS"  );
  return $buttons;
  }

  function mce_external_plugins( $plugin_array )
  {
  $plugin_array['wpgpsyntax']  =  plugin_dir_url( __FILE__ ). 'mce/wpol.js';
  return $plugin_array;
  }

  function tiny_mce_version( $version )
  {
  return ++$version;
  }

  }

  global $wpstocks_button_Obj;
  $wpstocks_button_Obj = new wpstocks_button();
*/

/*
 * AUTO-UPDATE FUNCTIONALITY
 */
if ( !class_exists( 'wpstocks_wp_auto_update' ) ) {

    class wpstocks_wp_auto_update
    {

        /**
         * The plugin current version
         * @var string
         */
        public $current_version;

        /**
         * The plugin remote update path
         * @var string
         */
        public $update_path;

        /**
         * Plugin Slug ( plugin_directory/plugin_file.php )
         * @var string
         */
        public $plugin_slug;

        /**
         * Plugin name ( plugin_file )
         * @var string
         */
        public $slug;

        /**
         * Initialize a new instance of the WordPress Auto-Update class
         * @param string $current_version
         * @param string $update_path
         * @param string $plugin_slug
         */
        function __construct( $current_version, $update_path, $plugin_slug )
        {

            $this->update_path = $update_path;

            // Set the class public variables
            $this->current_version = $current_version;

            $this->plugin_slug = $plugin_slug;
            list ( $t1, $t2 ) = explode( '/', $plugin_slug );
            $this->slug = str_replace( '.php', '', $t2 );

            // define the alternative API for updating checking
            add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update' ) );
            add_filter( 'site_transient_update_plugins', array( $this, 'check_update' ) ); //WP 3.0+
            add_filter( 'transient_update_plugins', array( $this, 'check_update' ) ); //WP 2.8+

            // Define the alternative response for information checking
            add_filter( 'plugins_api', array( &$this, 'check_info' ), 10, 3 );

        }

        /**
         * Add our self-hosted autoupdate plugin to the filter transient
         *
         * @param $transient
         * @return object $ transient
         */
        public function check_update( $transient )
        {

            if ( empty( $transient->checked ) ) {
                return $transient;
            }

            // Get the remote version
            $remote_version = $this->getRemote_version();

            // If a newer version is available, add the update
            if ( version_compare( $this->current_version, $remote_version, '<' ) ) {
                $obj = new stdClass();
                $obj->slug = $this->slug;
                $obj->new_version = $remote_version;
                $obj->url = $this->update_path;
                $obj->package = $this->update_path;
                $transient->response[$this->plugin_slug] = $obj;
            }
            return $transient;
        }

        /**
         * Add our self-hosted description to the filter
         *
         * @param boolean $false
         * @param array $action
         * @param object $arg
         * @return bool|object
         */
        public function check_info( $false, $action, $arg )
        {

            if ( $arg->slug === $this->slug ) {
                $information = $this->getRemote_information();
                return $information;
            }
            return false;
        }

        /**
         * Return the remote version
         * @return string $remote_version
         */
        public function getRemote_version()
        {
            $request = wp_remote_post( $this->update_path, array( 'body' => array( 'action' => 'version' ) ) );
            if ( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
                return $request['body'];
            }
            return false;
        }

        /**
         * Get information about the remote version
         * @return bool|object
         */
        public function getRemote_information()
        {
            $request = wp_remote_post( $this->update_path, array( 'body' => array( 'action' => 'info' ) ) );
            if ( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
                return unserialize( $request['body'] );
            }
            return false;
        }

        /**
         * Return the status of the plugin licensing
         * @return boolean $remote_license
         */
        public function getRemote_license()
        {
            $request = wp_remote_post( $this->update_path, array( 'body' => array( 'action' => 'license' ) ) );
            if ( !is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
                return $request['body'];
            }
            return false;
        }

    }

    add_action( 'init', 'wpstocks_wptuts_activate_au' );

    function wpstocks_wptuts_activate_au()
    {
        $wptuts_plugin_current_version = WPSTOCKS_CURRENT_VERSION;
        $wptuts_plugin_remote_path = WPSTOCKS_UPDATE_FILE_PATH;
        $wptuts_plugin_slug = plugin_basename( __FILE__ );
        new wpstocks_wp_auto_update( $wptuts_plugin_current_version, $wptuts_plugin_remote_path, $wptuts_plugin_slug );
    }

}

register_activation_hook( __FILE__, 'wpstocks_on_activation' );
register_deactivation_hook( __FILE__, 'wpstocks_deactivate' );

