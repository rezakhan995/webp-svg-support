<?php
namespace Webpsvg_Support\Core;

use Webpsvg_Support;

defined( 'ABSPATH' ) || wp_die( 'No access directly.' );

class Bootstrap {

    public static $instance = null;

    public static function instance() {

        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function init() {
        add_filter('plugin_action_links_' . Webpsvg_Support::plugins_basename(), [$this, 'insert_plugin_links']);
        
        add_filter('plugin_row_meta', [$this, 'insert_plugin_row_meta'], 10, 2);

        add_action( 'admin_menu', [$this, 'webpsvg_admin_menu'] );

        add_filter( 'mime_types', [$this, 'webpsvg_update_mime'], 9 );

        add_filter( 'upload_mimes', [$this, 'webpsvg_update_mime'], 9, 1 );

        if('yes' === get_option( 'webpsvg_allow_webp', '' )){
            add_filter( 'file_is_displayable_image', [$this, 'webpsvg_update_visibility'], 9, 2 );
        }

    }

    /**
     * Undocumented function
     *
     * @param [type] $links
     * @param [type] $file
     * @return void
     */
    public function insert_plugin_row_meta($links, $file) {

        $links[] = sprintf('<a href="%s" action="_blank"> %s </a>', 'https://wordpress.org/support/plugin/webp-svg-support/reviews/#new-post', esc_html__('Rate the plugin ★★★★★', 'webp-svg-support'));

        return $links;
    }


    /**
     * Undocumented function
     *
     * @param [type] $links
     * @return void
     */
    public function insert_plugin_links( $links ){
        $links[] = sprintf('<a href="%s" > %s </a>', admin_url() . 'admin.php?page=webp_svg_support', esc_html__('Settings', 'webp-svg-support'));
        return $links;
    }

    /**
     * Update Webp & SVG Upload Mime
     * 
     * @since 1.0.0
     *
     * @param [type] $mime_types
     * @return void
     */
    public function webpsvg_update_mime( $mime_types ) {
        if('yes' === get_option( 'webpsvg_allow_webp', '' )){
            $mime_types['webp'] = 'image/webp';
        }
        if('yes' === get_option( 'webpsvg_allow_svg', '' )){
            $mime_types['svg']  = 'image/svg+xml'; 
            $mime_types['svgz'] = 'image/svg+xml';
        }
        return $mime_types;
    }

    /**
     * Update Webp Image Visibility
     * 
     * @since 1.0.0
     *
     * @param [type] $result
     * @param [type] $path
     * @return void
     */
    public function webpsvg_update_visibility( $result, $path ) {

        if ( false === $result ) {
            $displayable_image_types = [ IMAGETYPE_WEBP ];
            $info                    = @getimagesize( $path );
            
            if ( empty( $info ) ) {
                $result = false;
            } elseif ( !in_array( $info[2], $displayable_image_types ) ) {
                $result = false;
            } else {
                $result = true;
            }

        }

        return $result;
    }


    /**
     * Admin Menu
     * 
     * @since 1.0.0
     * 
     * @return void
     */
    function webpsvg_admin_menu() {
        add_options_page( esc_html__( 'WebP & SVG Support', 'webp-svg-support' ),
            esc_html__( 'WebP & SVG Support', 'webp-svg-support' ),
            'manage_options',
            'webp_svg_support',
            [$this, 'webpsvg_options_page'] );
    }
    
    /**
     * Plugins Settings Page
     * 
     * @since 1.0.0
     *
     * @return void
     */
    public function webpsvg_options_page() {
        ?>
        <div class="webpsvg-wrap">
            <h2><?php echo esc_html__( 'WebP & SVG Support', 'webp-svg-support' );?></h2>
            <p>
                <form method="post" action="options.php">
                    <?php wp_nonce_field( 'update-options' );?>
                    <?php echo esc_html__( "By default, all webp and svg images are allowed to upload and display.", "webp-svg-support" );?><br />
                    <?php echo esc_html__( "You can change this by below options. You can enable / disable anyone or both by these options.", "webp-svg-support" );?><br /><br />
                    
                    <input class="webpsvg-input input-text" name="webpsvg_allow_webp" type="checkbox" id="webpsvg_allow_webp" value="yes" <?php echo ('yes' === get_option( 'webpsvg_allow_webp', '' )) ? 'checked' : ''; ?> />
                    <label for="webpsvg_allow_webp"><?php echo esc_html__('Allow WebP file upload','webp-svg-support');?></label><br>
                    <input class="webpsvg-input input-text" name="webpsvg_allow_svg" type="checkbox" id="webpsvg_allow_svg" value="yes" <?php echo ('yes' === get_option( 'webpsvg_allow_svg', '' )) ? 'checked' : ''; ?> />
                    <label for="webpsvg_allow_svg"><?php echo esc_html__('Allow SVG file upload','webp-svg-support');?></label><br>
    
                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="page_options" value="webpsvg_allow_webp,webpsvg_allow_svg" />
                    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"></p>
                </form>
            </p>
        </div>
        <?php
    }
    

}
