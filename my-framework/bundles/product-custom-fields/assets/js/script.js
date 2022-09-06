var Woo_Custom_Fields = {

    uploadProductImage: function () {
        var custom_uploader;
        jQuery('#woo_cf_image_btn').click(function(e) {

            e.preventDefault();

            //If the uploader object has already been created, reopen the dialog
            if (custom_uploader) {
                custom_uploader.open();
                return;
            }

            //Extend the wp.media object
            custom_uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose an Image',
                button: {
                    text: 'Choose an Image'
                },
                multiple: false
            });

            //When a file is selected, grab the URL and set it as the text field's value
            custom_uploader.on('select', function() {
                attachment = custom_uploader.state().get('selection').first().toJSON();
                jQuery('#woo_cf_image').val(attachment.url);
                jQuery('.woo_cf_image_field.img-preview img').attr('src', attachment.url);
                jQuery('.woo_cf_image_field.img-preview').removeClass('hidden');
            });

            //Open the uploader dialog
            custom_uploader.open();

        });
    },

    removeProductImage: function () {
        jQuery('#woo_cf_image').val('');
        jQuery('.woo_cf_image_field.img-preview img').attr('src', '');
        jQuery('.woo_cf_image_field.img-preview').addClass('hidden');
    },

    customFieldsActions: function () {
        let cf_tab         = jQuery('#product_custom_fields'),
            css_classes    = 'woo_cf_field_action button',
            save_btn_title = jQuery('#original_post_status').val() === 'publish' ? 'Update' : 'Publish';

        cf_tab.append('<button class="'+ css_classes +'" id="woo_cf_reset_btn">Reset custom fields</button>');
        cf_tab.append('<button class="'+ css_classes +' button-primary" id="woo_cf_publish_btn">' + save_btn_title + ' product</button>');
    },

    resetCustomFields: function () {
        jQuery('#woo_cf_reset_btn').click(function(e) {
            e.preventDefault();

            Woo_Custom_Fields.removeProductImage();
            jQuery('#woo_cf_published_date').val('');
            jQuery('#woo_cf_type option:first').prop('selected',true).trigger( "change" );
        });
    },

    updateProduct: function () {
        jQuery('#post').submit();
    },

    init: function () {
        Woo_Custom_Fields.customFieldsActions();
        Woo_Custom_Fields.resetCustomFields();
        Woo_Custom_Fields.uploadProductImage();

        jQuery('.remove-file-btn').click(function(e) {
            e.preventDefault();
            Woo_Custom_Fields.removeProductImage();
        });

    }
};

jQuery(document).ready( function(){
    Woo_Custom_Fields.init();
});