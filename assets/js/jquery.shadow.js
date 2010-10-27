(function($) {
	
	$.fn.shadowEnable  = function() { return $(this).find("+ .fx-shadow").show().end();   };
	$.fn.shadowDisable = function() { return $(this).find("+ .fx-shadow").hide().end();   };
	$.fn.shadowDestroy = function() { return $(this).find("+ .fx-shadow").remove().end(); };
	
	$.fn.shadow = function(options) {
		options = $.extend({
			offset:  1,
			opacity: 0.2,
			color:   "#000",
			monitor: false
		}, options || {});
		options.offset -= 1;
		
		return this.each(function() {
			
			// Remove an existing shadow if it exists
			var $element = $(this).shadowDestroy(),
			
			// Create a shadow element
			$shadow = $("<div class='fx-shadow' style='position: relative;'></div>").hide().insertAfter($element);
			
			// Figure the base height and width
			baseWidth  = $element.outerWidth(),
			baseHeight = $element.outerHeight(),
			
			// Get the offset
			position = $element.position(),
			
			// Get z-index
			zIndex = parseInt($element.css("zIndex")) || 0;
			
			// Append smooth corners
			$('<div class="fx-shadow-color fx-shadow-layer-1"></div>').css({ position: 'absolute', opacity: options.opacity - 0.05,  left: options.offset,     top: options.offset,     width: baseWidth + 1, height: baseHeight + 1 }).appendTo($shadow);
			$('<div class="fx-shadow-color fx-shadow-layer-2"></div>').css({ position: 'absolute', opacity: options.opacity - 0.10,  left: options.offset + 2, top: options.offset + 2, width: baseWidth,     height: baseHeight - 3 }).appendTo($shadow);
			$('<div class="fx-shadow-color fx-shadow-layer-3"></div>').css({ position: 'absolute', opacity: options.opacity - 0.10,  left: options.offset + 2, top: options.offset + 2, width: baseWidth - 3, height: baseHeight     }).appendTo($shadow);
			$('<div class="fx-shadow-color fx-shadow-layer-4"></div>').css({ position: 'absolute', opacity: options.opacity,         left: options.offset + 1, top: options.offset + 1, width: baseWidth - 1, height: baseHeight - 1 }).appendTo($shadow);
			
			// Add color
			$("div.fx-shadow-color", $shadow).css("background-color", options.color);
			
			// Set zIndex +1 and make sure position is at least relative
			// Attention: the zIndex will get one higher!
			$element
				.css({
					zIndex: zIndex + 1,
					position: ($element.css("position") == "static" ? "relative" : "")
				});
			
			// Copy the original z-index and position to the clone
			// alert(shadow); If you insert this alert, opera will time correctly!!
			$shadow.css({
				position:     "absolute",
				zIndex:       zIndex,
				top:          position.top+"px",
				left:         position.left+"px",
				width:        baseWidth,
				height:       baseHeight,
				marginLeft:   $element.css("marginLeft"),
				marginRight:  $element.css("marginRight"),
				marginBottom: $element.css("marginBottom"),
				marginTop:    $element.css("marginTop")
			}).fadeIn();
			
			
			if ( options.monitor ) {
				function rearrangeShadow() {
					var $element = $(this), $shadow = $element.next();
					// $shadow.css( $element.position() );
					$shadow.css({
						top:  parseInt($element.css("top"))  +"px",
						left: parseInt($element.css("left")) +"px"
					})
					$(">*", $shadow).css({ height: this.offsetHeight+"px", width: this.offsetWidth+"px" });
				}
			
				// Attempt to use DOMAttrModified event
				$element.bind("DOMAttrModified", rearrangeShadow);
			
				// Use expressions if they exist (IE)
				if( $shadow[0].style.setExpression ) {
					$shadow[0].style.setExpression("top" , "parseInt(this.previousSibling.currentStyle.top ) + 'px'");
					$shadow[0].style.setExpression("left", "parseInt(this.previousSibling.currentStyle.left) + 'px'");
				}
			}

		});
	};
	
})(jQuery);