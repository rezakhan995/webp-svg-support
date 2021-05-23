<?php
namespace Webpsvg_Support\Core;

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
        add_filter( 'mime_types', [$this, 'webp_modify_mimes'] );

        add_filter( 'upload_mimes', [$this, 'webp_modify_mimes'], 1, 1 );

        add_filter( 'file_is_displayable_image', [$this, 'webp_is_displayable'], 10, 2 );
    }

    public function webp_modify_mimes( $existing_mimes ) {
        $existing_mimes['webp'] = 'image/webp';
        return $existing_mimes;
    }

    public function webp_is_displayable( $result, $path ) {

        if ( $result === false ) {
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

    public function flush_rewrites() {
        flush_rewrite_rules();
    }

}
