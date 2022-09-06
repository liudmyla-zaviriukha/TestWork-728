<?php
/**
 * The template used for displaying create product form
 */

?>

<form id="create_product_form" action="" enctype="multipart/form-data">
    <p>
        <label for="product_title"><?php _e('Product Title:', 'storefront') ?></label>
        <input type="text" name="product_title" id="product_title" />
    </p>
    <p>
        <label for="product_price"><?php _e('Price:', 'storefront') ?></label>
        <input type="number" min="1" step="any" name="product_price" id="product_price" />
    </p>
    <p>
        <label for="product_image"><?php _e('Image:', 'storefront') ?></label>
        <!--<input type="file" name="product_image" id="product_image" />-->
        <input type="file" id="product_file" name="product_file" />
        <input type="hidden" name="product_file_field" id="product_file_field" />
        <label for="product_file"><a>Select a product image</a></label>
        <div id="product_filelist"></div>
    </p>
    <p>
        <label for="product_date"><?php _e('Published Date:', 'storefront') ?></label>
        <input type="date" name="product_date" id="product_date" />
    </p>
    <p>
        <label for="product_type"><?php _e('Type:', 'storefront') ?></label>
        <select id="product_type" name="product_type">
            <option value="rare" >Rare</option>
            <option value="frequent">Frequent</option>
            <option value="unusual">Unusual</option>
        </select>
    </p>

    <input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Let\'s go!', 'storefront' ); ?>">
</form>

<div class="clearfix"></div>
