<?php

namespace Bundles\Product_Custom_Fields;

class Init {

    private static $post_type = 'product';
    static $bundle_uri;
    static $bundle_path;
    private static $product_types = array(
        'rare'     => 'Rare',
        'frequent' => 'Frequent',
        'unusual'  => 'Unusual'
    );

    public static function static_init() {

        self::$bundle_uri  = str_replace( rtrim( ABSPATH, '/'), site_url(), dirname( __FILE__ ) );
        self::$bundle_uri  = str_replace( '\\', '/', self::$bundle_uri );

        self::$bundle_path = dirname( __FILE__ ) . '/';

        add_action( 'admin_enqueue_scripts', __CLASS__ . '::assets_admin_js' );

        if ( is_admin() ) {

            add_filter( 'woocommerce_product_data_tabs', __CLASS__ . '::product_new_tab' );
            add_action( 'woocommerce_product_data_panels', __CLASS__ . '::product_custom_fields' );
            add_action( 'woocommerce_process_product_meta', __CLASS__ . '::save_product_custom_fields' );

            add_action( 'save_post', __CLASS__ . '::save_product_custom_fields' );
        }
	}

    public static function assets_admin_js() {

        wp_enqueue_style( 'admin-product-cf-style',
            self::$bundle_uri . '/assets/css/style.css',
            array(),
            filemtime( self::$bundle_path . '/assets/css/style.css' )
        );
        wp_enqueue_script( 'admin-product-cf-script',
            self::$bundle_uri . '/assets/js/script.js',
            array( 'jquery' ),
            false,
            true
        );

    }

    public static function product_new_tab( $tabs ){

        $tabs['custom'] = array(
            'label'    => 'Custom Fields',
            'target'   => 'product_custom_fields',
            //'class'    => array( 'class1', 'class2' ),
            'priority' => 5,
        );
        return $tabs;

    }

    public static function product_custom_fields() {
        echo '<div id="product_custom_fields" class="panel woocommerce_options_panel">';

        woocommerce_wp_text_input( array(
            'id'            => 'woo_cf_image_btn',
            'type'          => 'button',
            'label'         => sanitize_text_field( 'Product Image' ),
            'value'         => 'Choose an Image',
            'class'         => 'button',
        ) );

        woocommerce_wp_text_input( array(
            'id'            => 'woo_cf_image',
            'type'          => 'hidden',
            'value'         => get_post_meta( get_the_ID(), 'woo_cf_image', true ),
            'data_type'     => 'url'
        ) );

        $url = get_post_meta( get_the_ID(),'woo_cf_image', true);
        echo '<p class="form-field woo_cf_image_field img-preview '. ($url == '' ? 'hidden' : '') . '"><label></label><img src="' . $url . '"/><span class="remove-file-btn"></span></p>';

        woocommerce_wp_text_input( array(
            'id'            => 'woo_cf_published_date',
            'type'          => 'date',
            'label'         => sanitize_text_field( 'Published Date' ),
            'value'         => get_post_meta( get_the_ID(), 'woo_cf_published_date', true ),
        ) );

        woocommerce_wp_select( array(
            'id'            => 'woo_cf_type',
            'label'         => sanitize_text_field( 'Product type' ),
            'options'       => self::$product_types,
            'value'         => get_post_meta( get_the_ID(), 'woo_cf_type', true ),
        ) );

        echo '</div>';
    }

    public static function save_product_custom_fields( $post_id ) {

        if ( ! ( isset( $_POST['woocommerce_meta_nonce'] ) || wp_verify_nonce( sanitize_key( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) ) {
            return false;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        $fields = [
            'woo_cf_image',
            'woo_cf_published_date',
            'woo_cf_type',
        ];
        foreach ( $fields as $field ) {
            if ( array_key_exists( $field, $_POST ) ) {
                update_post_meta( $post_id, $field, sanitize_text_field(  esc_attr( $_POST[$field] ) ) );
            }
        }

        if ( $_POST['woo_cf_image'] ) {
            $image_url    = sanitize_text_field(  esc_attr( $_POST['woo_cf_image'] ) );
            $thumbnail_id = self::get_image_id( $image_url );
            set_post_thumbnail( $post_id, $thumbnail_id );
        } else {
            delete_post_thumbnail( $post_id );
        }

//        $date = strtotime( $_POST['woo_cf_published_date'] );
//        $date_time = date( 'Y-m-d H:i:s', $date );
//        $update_post = array(
//            'ID'            => $post_id,
//            'post_date'     => $date_time,
//            'post_date_gmt' => get_gmt_from_date( $date_time )
//        );
//
//        wp_update_post( $update_post );

    }

    private static function get_image_id( $image_url ) {
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
        return $attachment[0];
    }


}