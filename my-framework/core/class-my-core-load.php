<?php


class My_Core {

    static $theme_uri;
    static $theme_path;
    static $textdomain;
    static $framework_path;
    static $framework_url;
    static $path_libs;
    static $libs_url;
    static $bundles_path;
    static $file_log_error;

    public static function load() {
        self::$theme_uri      = trailingslashit( get_stylesheet_directory_uri() );
        self::$theme_path     = trailingslashit( get_stylesheet_directory() );
        self::$framework_path = self::$theme_path . 'my-framework';
        self::$framework_url  = self::$theme_uri . 'my-framework';
        self::$bundles_path   = self::$framework_path . '/bundles';
        self::$path_libs      = self::$framework_path . '/libs/';
        self::$libs_url       = self::$framework_url . '/libs/';
        self::$file_log_error = ABSPATH . 'wp-content/my-framework.log';

        spl_autoload_register( __CLASS__ . '::autoload' );

        // Autoload bundles
        self::load_bundles();
    }

    private static function load_bundles() {
        if ( is_dir( self::$bundles_path ) ) {
            $iterator = new \DirectoryIterator ( self::$bundles_path );
            foreach ( $iterator as $dir ) {
                $dirname = $dir->getBasename();
                if ( $dir->isDir() && false === strpos( $dirname, '.' ) ) {

                    if ( false !== strpos( $dirname, '-' ) ) {
                        $bundle_folder_arr = explode( '-', $dirname );
                        $bundle            = self::make_namespace_by_bundle( $bundle_folder_arr );
                    } else {
                        $bundle = ucfirst( strtolower( $dirname ) );
                    }

                    $bundle_init = "Bundles\\$bundle\Init";

                    try {
                        if ( class_exists( $bundle_init ) ) {

                            if ( method_exists( $bundle_init, 'is_initialize' ) ) {
                                if ( ! $bundle_init::is_initialize() ) {
                                    continue;
                                }
                            }
                            if ( method_exists( $bundle_init, 'init' ) ) {
                                $bundle_init::init();
                            } else if ( method_exists( $bundle_init, 'static_init' ) ) {
                                $bundle_init::static_init();
                            }

                        }
                    } catch ( Throwable $t ) {
                        $line_error = current_time( 'mysql' ) . " - [error] file: " . $t->getFile() . '(' . $t->getLine() . ') - Message: ' . $t->getMessage() . "\r" . "Trace:" . $t->getTraceAsString();
                        if ( file_exists( self::$file_log_error ) ) {
                            file_put_contents( self::$file_log_error, $line_error, FILE_APPEND );
                        } else {
                            file_put_contents( self::$file_log_error, $line_error );
                        }
                    }
                }
            }
        }
    }

    private static function make_namespace_by_bundle( $bundle_folder_arr ) {
        $bundle_folder_arr = array_filter( $bundle_folder_arr );
        $bundle_folder_arr = array_map( function ( $el ) {
            return ucfirst( strtolower( $el ) );
        }, $bundle_folder_arr );

        return implode( '_', $bundle_folder_arr );

    }

    public static function autoload( $class ) {
        $base_dir        = self::$framework_path . '/';
        $relative_class  = str_replace( '_', '-', $class );
        $file            = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';
        $file_name       = basename( $file );
        $class_file_name = 'class-' . strtolower( str_replace( '_', '-', $file_name ) );
        $file            = strtolower( str_replace( $file_name, $class_file_name, $file ) );
        if ( file_exists( $file ) ) {
            require_once $file;
        }
    }

}

My_Core::load();
