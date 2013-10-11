<?php
/**
 * UsabilityDynamics\Veneer Bootstrap
 *
 * @verison 0.0.1
 * @author potanin@UD
 * @namespace UsabilityDynamics\Veneer
 */
namespace UsabilityDynamics\Veneer {

  // Seek ./vendor/autoload.php and autoload
  if( is_file( basename( __DIR__ ) . DIRECTORY_SEPARATOR . 'vendor/autoload.php' ) ) {
    include_once( basename( __DIR__ ) . DIRECTORY_SEPARATOR . 'vendor/autoload.php' );
  }

  use UsabilityDynamics\Utility;
  use UsabilityDynamics\Settings;

  /**
   * Bootstrap Veneer
   *
   * @class Bootstrap
   * @author potanin@UD
   * @version 0.0.1
   */
  class Bootstrap {

    /**
     * Veneer core version.
     *
     * @static
     * @property $version
     * @type {Object}
     */
    public static $version = '0.1.2';

    /**
     * Textdomain String
     *
     * @public
     * @property text_domain
     * @var string
     */
    public static $text_domain = 'veneer';

    /**
     * Singleton Instance Reference.
     *
     * @public
     * @static
     * @property $instance
     * @type {Object}
     */
    public static $instance = false;

    /**
     * Constructor.
     *
     * UsabilityDynamics components should be avialable.
     * - class_exists( '\UsabilityDynamics\API' );
     * - class_exists( '\UsabilityDynamics\Utility' );
     *
     * @for Loader
     * @method __construct
     */
    public function __construct() {

      // Return singleton instance
      if( self::$instance ) {
        return self::$instance;
      }

      // Save context reference.
      self::$instance = & $this;

      // Initialize Controllers and Helpers
      $this->_developer     = new Developer();

      // Fix MultiSite URLs
      $this->fix_urls();

      // Initialize all else.
      add_action( 'plugins_loaded', array( __CLASS__, 'plugins_loaded' ) );
      add_action( 'admin_bar_menu', array( __CLASS__, 'admin_bar_menu' ), 21 );

    }

    /**
     * Initializer.
     *
     * @method plugins_loaded
     * @author potanin@UD
     */
    private function plugins_loaded() {
      add_action( 'admin_init', array( $this, 'admin_init' ) );

      // add_action( 'shutdown', array( $this, 'shutdown' ) );
      // add_action( 'init', array( $this, 'init' ) );
      // add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
      // add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
      // add_action( 'wp_loaded', array( $this, 'wp_loaded' ) );
      // add_action( 'template_redirect', array( $this, 'template_redirect' ) );

    }

    /**
     * Initialize Admin
     *
     * @method admin_init
     * @author potanin@UD
     */
    private function admin_init() {

      /* Remove Akismet API Key Nag */
      remove_action( 'admin_notices', 'akismet_warning' );

      /* Disable BuddyPress Nag */
      remove_action( 'admin_notices', 'bp_core_update_nag', 5 );
      remove_action( 'network_admin_notices', 'bp_core_update_nag', 5 );

    }

    /**
     * Update Amin Menu
     *
     * @method admin_bar_menu
     * @author potanin@UD
     */
    private function admin_bar_menu( $wp_admin_bar = false ) {

      if( !is_super_admin() || !is_multisite() || !$wp_admin_bar ) {
        return;
      }

      $wp_admin_bar->add_menu( array(
        'parent' => 'network-admin',
        'id'     => 'network-themes',
        'title'  => __( 'Themes' ),
        'href'   => network_admin_url( 'themes.php' ),
      ) );

      $wp_admin_bar->add_menu( array(
        'parent' => 'network-admin',
        'id'     => 'network-plugins',
        'title'  => __( 'Plugins' ),
        'href'   => network_admin_url( 'plugins.php' ),
      ) );

      $wp_admin_bar->add_menu( array(
        'parent' => 'network-admin',
        'id'     => 'network-plugins',
        'title'  => __( 'Settings' ),
        'href'   => network_admin_url( 'settings.php' ),
      ) );

    }

    /**
     * Automatically fix MS URLs that get messed up
     *
     */
    private function fix_urls() {

      add_filter( 'network_site_url', function ( $url ) {
        //if( !strpos( $url, '/system' ) ) { return trailingslashit( $url ) . 'system/'; }
        return str_replace( 'wp-admin', 'system/wp-admin', $url );
      } );

      add_filter( 'blog_option_upload_path', function ( $url ) {

        // In WP 3.5 the UPLOADS constant sets the uploads path relative to the ABSPATH
        if( defined( 'UPLOADS' ) ) {

        }

        // Legacy WordPress MS
        if( strpos( $url, 'wp-content/blogs.dir' ) !== false ) {
          return str_replace( 'wp-content/blogs.dir', 'media/sites', $url );
        }

        // Contemporary WordPress MS
        if( strpos( $url, 'wp-content/sites' ) !== false ) {
          return str_replace( 'wp-content/sites', 'media/sites', $url );
        }

        if( strpos( $url, 'wp-content/uploads' ) !== false ) {
          return str_replace( 'wp-content/uploads', 'media/sites', $url );
        }

        return $url;

      } );

    }

    /**
     * Triggered on WordPress Shutdown
     *
     * @author potanin@UD
     * @for Veneer
     */
    public static function shutdown() {
    }

    /**
     * Error Handler
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     *
     * @param $errfile
     *
     * @return bool
     */
    public static function error_handler( $errno = null, $errstr = '', $errfile = null , $errline = null ) {

      die('Veneer error' );

      // This error code is not included in error_reporting
      if( !( error_reporting() & $errno ) ) {
        return;
      }

      switch( $errno ) {

        // Fatal
        case E_ERROR:
        case E_CORE_ERROR:
        case E_RECOVERABLE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
          wp_die( "<h1>Website Temporarily Unavailable</h1><p>We apologize for the inconvenience and will return shortly.</p>" );
          break;

        // Do Nothing
        case E_WARNING:
        case E_USER_NOTICE:
          return true;
          break;

        // No Idea.
        default:
          return;
          // wp_die( "<h1>Website Temporarily Unavailable</h1><p>We apologize for the inconvenience and will return shortly.</p>" );
          break;
      }

      return true;

    }

    /**
     * Get Setting.
     *
     *    // Get Setting
     *    Veneer::get( 'my_key' )
     *
     * @method get
     *
     * @for Flawless
     * @author potanin@UD
     * @since 0.1.1
     */
    public static function get( $key, $default = null ) {
      return self::$instance->_settings ? self::$instance->_settings->get( $key, $default ) : null;
    }

    /**
     * Set Setting.
     *
     * @usage
     *
     *    // Set Setting
     *    Veneer::set( 'my_key', 'my-value' )
     *
     * @method get
     * @for Flawless
     *
     * @author potanin@UD
     * @since 0.1.1
     */
    public static function set( $key, $value = null ) {
      return self::$instance->_settings ? self::$instance->_settings->set( $key, $value ) : null;
    }

    /**
     * Get the Veneer Singleton
     *
     * Concept based on the CodeIgniter get_instance() concept.
     *
     * @example
     *
     *      var settings = Veneer::get_instance()->Settings;
     *      var api = Veneer::$instance()->API;
     *
     * @static
     * @return object
     *
     * @method get_instance
     * @for Veneer
     */
    public static function &get_instance() {
      return self::$instance;
    }

  }

}