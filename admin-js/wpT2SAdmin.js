jQuery(document).ready(function ($) {

	var current_ico = null;

	$('.upload_icon_button').click(function () {
		current_ico = $(this).parent().find("input[type=text]");
        tb_show('Upload Icon', 'media-upload.php?type=image&TB_iframe=true&post_id=0', false);
        return false;
	});
	
	window.send_to_editor = function (html) {
		$(current_ico).val( $(html).attr("src") );
		tb_remove();
	}
});

