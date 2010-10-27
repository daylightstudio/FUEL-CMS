(function($){
	jQuery.fn.inputLabel = function(settings) {
		settings = jQuery.extend({
			focusClass: 'focusClass',
			autoFocus: false
		}, settings);
		return this.each(function(){
			var $this = $(this);
			var $prev = $(this).prev();
			
			$this.css({position: 'relative', backgroundColor: 'transparent', zIndex: 2, border: 0});
			$prev.css({position: 'absolute', zIndex: 1});
			
			var hiddenCss = {textIndent: '-5000px', overflow: 'hidden'};
			if ($this.val() == ''){
				$this.focus(function(){
					if ($this.val() != ''){
						$prev.css(hiddenCss);
					}
					$prev.addClass(settings.focusClass);

				});
				$this.blur(function(){
					if ($this.val() == ''){
						$prev.css('textIndent', '0px');
					}
					$prev.removeClass(settings.focusClass)
				});

				$this.keypress(function(){
					$prev.css(hiddenCss);
				});
				if (settings.autoFocus) $(this).focus()
			} else {
				$prev.css(hiddenCss);
			}
			return this;
		});
	};
})(jQuery);