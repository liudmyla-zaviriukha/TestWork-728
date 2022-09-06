<?php

namespace Bundles\Include_Css_Js;

class Init {

	public static function static_init() {
		add_action( 'init', __CLASS__ . '::init' );
	}

	public static function init() {
		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::assets_admin_css' );
		} else {
			add_action( 'wp_enqueue_scripts', __CLASS__ . '::assets_css', 20 );
			add_action( 'wp_enqueue_scripts', __CLASS__ . '::assets_js', 20 );
		}

		add_action( 'footer_main_script_js', __CLASS__ . '::footer_main_script_js', 10 );
	}

	public static function assets_admin_css() {
        wp_enqueue_style(
            'admin-style',
            \My_Core::$theme_uri . 'assets/css/admin.css',
            array(),
            filemtime( \My_Core::$theme_path . 'assets/css/admin.css' )
        );
	}

	public static function assets_js() {
		wp_enqueue_script( 'jquery' );

		wp_enqueue_script( 'deco-scripts.min',
			\My_Core::$theme_uri . 'assets/js/scripts.min.js',
			array( 'jquery' ),
			filemtime( \My_Core::$theme_path . 'assets/js/scripts.min.js' ),
			true
		);

	}

    public static function assets_css() {
        if ( file_exists( \My_Core::$theme_path . 'assets/css/style.css' ) ) {
            wp_enqueue_style(
                'style-css',
                \My_Core::$theme_uri . 'assets/css/style.css',
                array(),
                filemtime( \My_Core::$theme_path . 'assets/css/style.css' )
            );
        }
    }

    public static function footer_main_script_js() {
        ?>
        <script>
            var js_vars = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php' ); ?>"};
            var $ = jQuery;
        </script>
        <?php
    }

}