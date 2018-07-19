
// debug for iOS
var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === "[object SafariRemoteNotification]"; })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));
var ua = navigator.userAgent.toLowerCase(); 
if (isSafari == false && ua.indexOf('safari') != -1) { 
	isSafari = true;
}

(function($){

	var audio = null;

	$.fn.wpT2S = function () {

		//wrapping audio element into title of t2s section and add event to click
		$("." + wpT2S_content_class_selector).find("h1, h2, h3, h4, h5, h6").addClass("titleT2S").append('<span class="playerT2S wpT2S_Icon_Base"></span>');

		//event click
		$("." + wpT2S_content_class_selector).on("click",".playerT2S", function () { 

			if( audio != null ){
				audio.pause();
			}

			//save this
			var el = $(this);
			
			//manage icon
			var action = ""; 
		
			if ($(el).hasClass("wpT2S_Icon_Pause")) {
				action = "pause"
			} else if ($(el).hasClass("wpT2S_Icon_Play")) {
				action = "play"
			} else {

				//request sound
				if( !$(el).hasClass("loading") ){
					$.ajax({
						url: wpT2S_ajaxURL,
						async: true,
						method: "POST",
						data: {
							'action': 'wpT2S',
							'text': $(this).closest("." + wpT2S_content_class_selector).text()
						},
						beforeSend:function(){
							$(el).removeClass("wpT2S_Icon_Pause wpT2S_Icon_Play wpT2S_Icon_Base").addClass("wpT2S_Icon_Loading");
						},
						success: function (response) {
							if (response.url != undefined && response.url != "") {
	
								audio = new Audio( response.url );
								audio.controls = false;
		
								if( isSafari ){
									audio.pause();
									$(el).removeClass("wpT2S_Icon_Base").addClass("wpT2S_Icon_Play");
								}else{
									audio.play();
									$(el).removeClass("wpT2S_Icon_Base").addClass("wpT2S_Icon_Pause");
								}
	
							}
						}
					});
				}
			}

			if( action == "pause"){
				audio.pause();
				$(el).removeClass("wpT2S_Icon_Loading wpT2S_Icon_Pause wpT2S_Icon_Base").addClass("wpT2S_Icon_Play");
			}else if( action == "play" ){
				audio.play();
				$(el).removeClass("wpT2S_Icon_Loading wpT2S_Icon_Play wpT2S_Icon_Base").addClass("wpT2S_Icon_Pause");	
			}

		});


	}

	var list_wpT2S_content = $("." + wpT2S_content_class_selector);	
	if (list_wpT2S_content.length > 0) {
		list_wpT2S_content.wpT2S();
	}

})(jQuery);