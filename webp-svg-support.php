<?php
/**
 * Plugin Name:       WebP & SVG Support
 * Plugin URI:        https://wordpress.org/plugins/webp-svg-support/
 * Description:       Allows WebP and SVG image file upload into WordPress media and sanitizes before saving it.
 * Version:           1.3.0
 * Author:            Reza Khan
 * Author URI:        https://www.reza-khan.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       webp-svg-support
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || wp_die( 'No access directly.' );

/**
 * Main function
 *
 * @since 1.0.0
 */
class Webpsvg_Support {

    /**
     * Instance of Webpsvg_Support class.
     *
     * @since 1.0.0
     *
     * @var Webpsvg_Support $instance Holds the class singleton instance.
     */
    public static $instance = null;

    /**
     * Returns singleton instance of current class.
     *
     * @since 1.0.0
     *
     * @return Webpsvg_Support
     */
    public static function init() {

        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Constructor function for Webpsvg_Support class.
     *
     * @since 1.0.0
     *
     * @return Webpsvg_Support
     */
    public function __construct() {
        add_action( 'init', [$this, 'i18n'] );
        add_action( 'plugins_loaded', [$this, 'initialize_modules'] );
    }

    /**
     * Loads plugin textdomain directory.
     *
     * @since 1.0.0
     */
    public function i18n() {
        load_plugin_textdomain( 'webp-svg-support', false, self::plugin_dir() . 'languages/' );
    }

    /**
     * Initialize plugin modules.
     *
     * @since 1.0.0
     */
    public function initialize_modules() {
        do_action( 'webpsvg/before_load' );

        require_once self::core_dir() . 'bootstrap.php';
        Webpsvg_Support\Core\Bootstrap::instance()->init();

        do_action( 'webpsvg/after_load' );
    }

    /**
     * Sets an option when plugin activation hook is called.
     *
     * @since 1.0.0
     *
     * @return void
     */
    static function webpsvg_activate() {
        // do something
        update_option( "webpsvg_allow_webp", 'yes' );
        update_option( "webpsvg_allow_svg", 'yes' );
    }

    /**
     * Sets an option when plugin deactivation hook is called.
     *
     * @since 1.0.0
     *
     * @return void
     */
    static function webpsvg_deactivate() {
        // do something
        delete_option( 'webpsvg_allow_webp' );
        delete_option( 'webpsvg_allow_svg' );
    }

    /**
     * Plugin Version.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public static function version() {
        return '1.3.0';
    }

    /**
     * Core Url.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public static function core_url() {
        return trailingslashit( self::plugin_url() . 'core' );
    }

    /**
     * Core Directory Path.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public static function core_dir() {
        return trailingslashit( self::plugin_dir() . 'core' );
    }

    /**
     * Plugin Url.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public static function plugin_url() {
        return trailingslashit( plugin_dir_url( self::plugin_file() ) );
    }

    /**
     * Plugin Directory Path.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public static function plugin_dir() {
        return trailingslashit( plugin_dir_path( self::plugin_file() ) );
    }

    /**
     * Plugins Basename.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public static function plugins_basename() {
        return plugin_basename( self::plugin_file() );
    }

    /**
     * Plugin File.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public static function plugin_file() {
        return __FILE__;
    }

}

/**
 * Load Webpsvg_Support plugin when all plugins are loaded
 *
 * @since 1.0.0
 *
 * @return Webpsvg_Support
 */
function webpsvg() {
    return Webpsvg_Support::init();
}

// Let's go...
webpsvg();

/* Do something when the plugin is activated? */
register_activation_hook( __FILE__, ['Webpsvg_Support', 'webpsvg_activate'] );

/* Do something when the plugin is deactivated? */
register_deactivation_hook( __FILE__, ['Webpsvg_Support', 'webpsvg_deactivate'] );
