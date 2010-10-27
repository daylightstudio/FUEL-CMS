Cufon.replace('h1, h2, .cufon, .hdr, .btn', { hover: true});

$(function(){

	$(function(){
		$('.submit').click(function(){
			$(this).parents('form').submit();
			return false;
		})
		$('.enter_email').inputLabel();

	});
	
	
	
	// screenshots
	var SCREENSHOT_REVEAL_SPEED = 300;
	var SCREENSHOT_ORIG_HEIGHT = $('#screenshots').height();
	var screenshotsInited = false;

	$('#btn_screenshots').toggle(
		function(){
			$('#screenshots').show().css('height', 0)
			.animate({
				height: SCREENSHOT_ORIG_HEIGHT +'px',
	        	opacity: 1
				},
				SCREENSHOT_REVEAL_SPEED,
				function(){
					if (!screenshotsInited){
						$('#screenshots').viewer({displayNum:4, showNumbers: false});
						screenshotsInited = true;
					}
				});
		},
		function(){
			$('#screenshots')
			.animate({
				height: 0,
	        	opacity: 0
				},
				SCREENSHOT_REVEAL_SPEED,
				function(){
					$(this).hide();
				});
			
		}
	);
	
	// zoom of screenshots
	$.fn.fancyzoom.defaultsOptions.imgDir= imgPath + 'zoom/';
	
	$('a.zoom').fancyzoom({Speed:300, showZoomIndicator:false});
});
