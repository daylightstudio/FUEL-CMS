/*
(c) Copyrights 2011

Author David McReynolds
Daylight Studio
dave@thedaylightstudio.com

// requires super cookie plugin if cookies is used
*/
;(function($){
	jQuery.fn.simpleTab = function(settings) {
		var s = jQuery.extend({
			startIndex: 0,
			childrenSelector: null,
			cookie: false
			 }, 
		settings);
		
		var activeTab = null;
		var activeContent = null;
		var hasCookie = (s.cookie && s.cookie.group && s.cookie.name);
		return this.each(function(){
			
			var _this = this;

			// hide them all at first
			$(this).find('a').each(function(i){
				var id = $(this).attr('href');
				$(id).hide();
			})
			
			// set tab click handler
			$children = (s.childrenSelector) ? $(this).find(s.childrenSelector) : $(this).children();
			
			$children.click(function(){
				$this = $(this);
				var index = $this.index();
				if (isNaN(index)) index = 0;
				// hide active if it isn't active tab
				if (!activeTab || $this.find('a').attr('href') != activeTab.find('a').attr('href')){
					if (activeTab) activeTab.removeClass('active');
					if (activeContent) activeContent.hide();

					// highlight the active tab
					$this.addClass('active');

					// show the contents
					var id = $(this).find('a').attr('href'); // remove the #
					activeContent = $(id);
					activeContent.show();
					
					activeTab = $this;
					$(_this).trigger('tabClicked', [index, activeTab, activeContent, s]);
					if (hasCookie){
						$.supercookie(s.cookie.group, s.cookie.name, index.toString(), s.cookie.params);
					} else {
						s.startIndex = 0;
					}
				}
				
				return false;
			});
			
			if (hasCookie){
				s.startIndex = parseInt($.supercookie(s.cookie.group, s.cookie.name));
			}

			if (isNaN(s.startIndex) || ($children.size() -1) < s.startIndex) s.startIndex = 0;
			$(this).children().eq(s.startIndex).click();
			
			return this;
		});
	};
})(jQuery);