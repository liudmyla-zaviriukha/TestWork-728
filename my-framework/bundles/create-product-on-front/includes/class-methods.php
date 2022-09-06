<?php

namespace Bundles\Create_Product_On_Front\Includes;

class Methods {

	public static function init() {

		add_action( 'wp_ajax_create_product', __CLASS__ . '::create_product' );
		add_action( 'wp_ajax_nopriv_create_product', __CLASS__ . '::create_product' );

        add_action( 'wp_ajax_product_image_upload', __CLASS__ . '::product_image_upload' );
        add_action( 'wp_ajax_nopriv_product_image_upload', __CLASS__ . '::product_image_upload' );

	}

    public static function product_image_upload() {
        $upload_dir = wp_upload_dir();

        if ( isset( $_FILES[ 'product_file' ] ) ) {
            $path = $upload_dir[ 'path' ] . '/' . basename( $_FILES[ 'product_file' ][ 'name' ] );

            if( move_uploaded_file( $_FILES[ 'product_file' ][ 'tmp_name' ], $path ) ) {

                $attach_id = wp_insert_attachment( array(
                    'guid'              =>  $upload_dir[ 'url' ] . '/' . basename( $_FILES[ 'product_file' ][ 'name' ] ),
                    'post_mime_type'    =>  $_FILES[ 'product_file' ]['type'],
                    'post_title'        =>  preg_replace( '/\.[^.]+$/', '', $_FILES[ 'product_file' ][ 'name' ]),
                    'post_content'      =>  '',
                    'post_status'       =>  'inherit',
                ), $path, get_the_ID());

                // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
                require_once( ABSPATH . 'wp-admin/includes/image.php' );

                // Generate the metadata for the attachment, and update the database record.
                $attach_data = wp_generate_attachment_metadata($attach_id, $path);
                wp_update_attachment_metadata( $attach_id, $attach_data );

                echo $upload_dir[ 'url' ] . '/' . basename( $_FILES[ 'product_file' ][ 'name' ] );
            }

        }
        die;
    }

	public static function create_product() {

		$title = empty( $_POST['title'] ) ? '' : $_POST['title'];
		$type  = empty( $_POST['type'] ) ? '' : $_POST['type'];
		$date  = empty( $_POST['date'] ) ? '' : $_POST['date'];
		$image = empty( $_POST['image'] ) ? '' : $_POST['image'];
		$price = empty( $_POST['price'] ) ? '' : $_POST['price'];

		if ( ! empty( $title ) && ! empty( $price ) && ! empty( $date ) && !empty( $image ) ) {

			$args = array(
				'post_type'    => 'product',
				'post_status'  => 'publish',
				'post_title'   => sanitize_text_field( $title ),
			);

			$post = wp_insert_post( $args );

			if ( ! is_wp_error( $post ) ) {

				update_post_meta( $post, 'woo_cf_type', $type );
				update_post_meta( $post, 'woo_cf_image', sanitize_text_field( $image ) );
				update_post_meta( $post, 'woo_cf_published_date', $date );

                if ( $price !== '' && $price >= 0 ) {
                    update_post_meta( $post, '_regular_price', $price );
                    update_post_meta( $post, '_price', $price );
                }

                global $wpdb;
                $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", sanitize_text_field( $image) ));
                set_post_thumbnail( $post, $attachment[0] );

//                $date = strtotime( $date );
//                $date_time = date( 'Y-m-d H:i:s', $date );
//                $update_post = array(
//                    'ID'            => $post->ID,
//                    'post_date'     => $date_time,
//                    'post_date_gmt' => get_gmt_from_date( $date_time )
//                );
//
//                wp_update_post( $update_post );

				wp_send_json_success( array(
					'success'    => true,
					'message'    => __( 'Your product was successfully created!', 'storefront' )
				) );
			}
		}

		wp_send_json_error( array( 'message' => __( 'Please, fill in all required fields.', 'storefront' ) ) );

	}

}