console.log( wpT2S_ajaxURL );
console.log(wpT2S_content_class_selector);

/*
jQuery(function ( $ ) { 
	var list_wpT2S_content = $("." + wpT2S_content_class_selector);	
	if(list_wpT2S_content.length > 0){ 

	}
});*/

(function($){

	$.fn.wpT2S = function () {

		//wrapping audio element into title of t2s section and add event to click
		$("." + wpT2S_content_class_selector).find("h1, h2, h3, h4, h5").append('<span class="player_t2s"></span>').click(function () { 

			$.ajax({
				url: wpT2S_ajaxURL,
				async: true,
				method: "POST",
				data: {
					'action': 'wpT2S',
					'text': $(this).parent().text()
				},
				success: function (response) {
					console.log(response);
			    }
			});

		});


	}

	var list_wpT2S_content = $("." + wpT2S_content_class_selector);	
	if (list_wpT2S_content.length > 0) {
		list_wpT2S_content.wpT2S();
	}

})(jQuery);