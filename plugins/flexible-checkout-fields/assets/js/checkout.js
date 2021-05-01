jQuery(document).on("click",".inspire-file-add-button",function() {
	jQuery(this).parent().find('input[type=file]').click();
});

jQuery(document).on("click",".inspire-file-delete-button",function() {
	jQuery(this).parent().find('input[type=file]').val('');
	jQuery(this).parent().find('input[type=text]').val('');
	jQuery(this).parent().find('.inspire-file-info').empty();
	jQuery(this).parent().find('.inspire-file-info').hide();
	jQuery(this).parent().find('.inspire-file-delete-button').hide();
	jQuery(this).parent().find('.inspire-file-add-button').show();
});

jQuery(document).on("change",".inspire-file-file",function() {
	var id = jQuery(this).parent().attr('id');
	var $file_info = jQuery('#' + id).find('.inspire-file-info');
	var $file_error = jQuery('#' + id).find('.inspire-file-error');
	var $file_add_button = jQuery('#' + id).find('.inspire-file-add-button');
	$file_info.empty();
	$file_error.empty();
	$file_error.hide();
	$file_info.show();
	$file_info.append( words.uploading );
	jQuery(this).parent().find('input[type=text]').val(jQuery(this).val());
	$file_add_button.hide();
	var fd = new FormData();
	var file = jQuery(this).prop('files')[0];
	var filename = file.name;
    fd.append(jQuery(this).attr('field_name'), file);
    fd.append( 'action', 'cf_upload' );
    fd.append( 'inspire_upload_nonce', inspire_upload_nonce );

    jQuery('#place_order').prop('disabled',true);

    jQuery.ajax({
        type: 'POST',
        url: fcf_ajaxurl,
        data: fd,
        contentType: false,
        processData: false,
        success: function(response){
            jQuery('#place_order').prop('disabled',false);
            if ( response != 0 ) {
            	response = JSON.parse(response);
            	if ( response.status != 'ok' ) {
					$file_add_button.show();
					$file_info.empty();
					$file_info.hide();
					$file_error.empty();
					$file_error.append( response.message + '<br/>' );
					$file_error.show();
                    jQuery('#' + id).find('.inspire-file-file').val('');
                    jQuery('#' + id).find('.inspire-file').val('');
            	}
            	else {
            		jQuery('#' + id).find('.inspire-file-delete-button').show();
					$file_error.empty();
					$file_error.hide();
					$file_info.empty();
					$file_info.show();
					$file_info.append(filename + '<br/>');
            	}
            }
        }
    });
});
