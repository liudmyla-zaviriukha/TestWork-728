<?php

namespace Bundles\Create_Product_On_Front;

class Init {

    private static $post_type = 'product';
    static $bundle_uri;
    static $bundle_path;


    public static function static_init() {

        self::$bundle_uri  = str_replace( rtrim( ABSPATH, '/'), site_url(), dirname( __FILE__ ) );
        self::$bundle_uri  = str_replace( '\\', '/', self::$bundle_uri );

        self::$bundle_path = dirname( __FILE__ ) . '/';

        Includes\Methods::init();

        add_action( 'wp_enqueue_scripts', __CLASS__ . '::assets_js' );

	}

    public static function assets_js() {

        wp_enqueue_script( 'create-product-script',
            self::$bundle_uri . '/assets/js/script.js',
            array( 'jquery' ),
            false,
            true
        );

    }


}