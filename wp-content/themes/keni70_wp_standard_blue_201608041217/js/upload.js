jQuery.noConflict();
(function($) {
	$('.keni_upload_image_button').click(function() {
		var upload_image_button_no = $(this).attr("id");
		var id = upload_image_button_no.match(/\d+$/);
		if (id > 0) {
			formfield =$('#keni_upload_image_'+id).attr('name');
			tb_show('', 'media-upload.php?type=image&post_id=&TB_iframe=true');

			window.original_send_to_editor = window.send_to_editor;

			window.send_to_editor = function(html) {
				if (formfield) {						
					imgurl_match = html.match(/src="(.*?)"/);
					if (imgurl_match[1] != "") {
						$('#keni_upload_image_'+id).val(imgurl_match[1]);
						$('#keni_img_'+id).html('<img src="'+imgurl_match[1]+'" />');
						tb_remove();
					} else {
						$('#keni_upload_image_'+id).val('');
						$('#keni_img_'+id).html('');
					}
				}
				window.send_to_editor = window.original_send_to_editor;
			}
			return false;
		}
	});
})(jQuery);