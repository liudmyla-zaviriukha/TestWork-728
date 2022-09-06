<?php
/**
 * Template name: Create Product Page
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <?php
            if( current_user_can( 'publish_posts' ) ) {
                while (have_posts()) :
                    the_post();

                    do_action('storefront_page_before');

                    get_template_part('content', 'page');

                    get_template_part('content', 'create-product-form');

                    do_action('storefront_page_after');

                endwhile; // End of the loop.
            } else {
                echo 'Sorry you are not allowed to access this page';
            }
            ?>

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
do_action( 'footer_main_script_js' );

get_footer();