var Create_Product_On_Front = {

    validateNotEmptyField: function(value, minimumLength) {
        var result = false;
        if (typeof (minimumLength) === 'undefined') {
            minimumLength = 1;
        }
        if (value.toString().length >= minimumLength) {
            result = true;
        }
        return result;
    },

    createProduct: function(form) {

        var title = form.find('#product_title'),
            price = form.find('#product_price'),
            date  = form.find('#product_date'),
            type  = form.find('#product_type'),
            image = form.find('#product_file_field'),
            submit = form.find('.button'),
            error = false;

        if (Create_Product_On_Front.validateNotEmptyField(price.val()) === false) {
            price.addClass('error');
            error = true;
        }
        if (Create_Product_On_Front.validateNotEmptyField(title.val()) === false) {
            title.addClass('error');
            error = true;
        }
        if (Create_Product_On_Front.validateNotEmptyField(image.val()) === false) {
            image.addClass('error');
            error = true;
        }
        if (Create_Product_On_Front.validateNotEmptyField(date.val()) === false) {
            date.addClass('error');
            error = true;
        }

        if (error) {
            return;
        }

        submit.disabled = true;
        submit.prop('disabled', 'disabled');

        jQuery.ajax({
            type: 'POST',
            url: js_vars.ajaxurl,
            dataType: 'json',
            data: {
                action: 'create_product',
                title : title.val(),
                image : image.val(),
                date  : date.val(),
                price : price.val(),
                type  : type.find('option:selected').val(),
            },
            success: function ( response ) {
                if (response.success === true) {
                    title.val('');
                    price.val('');
                    image.val('');
                    date.val('');
                    type.find('option:first').prop('selected',true).trigger('change');
                    jQuery('input').removeClass('error');
                    jQuery('#product_filelist').empty();
                    form.append( '<div id="success_message">'+response.data.message+'</div>' ).delay(10000).queue(function(){
                        jQuery('#success_message').remove();
                        jQuery(this).dequeue();
                    });
                }

                submit.disabled = false;
                submit.prop('disabled', false);
            },
            error: function(e) {
                console.log(e);
                submit.disabled = false;
                submit.prop('disabled', false);
            },
        });

    },

    init: function() {
        jQuery('#create_product_form').submit(function (e) {
            e.preventDefault();
            var create_product_form = jQuery(this);
            Create_Product_On_Front.createProduct(create_product_form);
        });

        jQuery( '#product_file' ).change( function() {

            if ( ! this.files.length ) {
                jQuery( '#product_filelist' ).empty();
            } else {

                // we need only the only one for now, right?
                const file = this.files[0];

                jQuery( '#product_filelist' ).html( '<img src="' + URL.createObjectURL( file ) + '"><span>' + file.name + '</span>' );

                const formData = new FormData();
                formData.append( 'product_file', file );

                jQuery.ajax({
                    url: js_vars.ajaxurl + '?action=product_image_upload',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    enctype: 'multipart/form-data',
                    processData: false,
                    success: function ( response ) {
                        jQuery( 'input[name="product_file_field"]' ).val( response );
                    }
                });

            }

        } );

    },
};

jQuery(document).ready(function() {
    Create_Product_On_Front.init();
});