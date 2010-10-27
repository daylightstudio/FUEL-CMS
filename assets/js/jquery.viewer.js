(function($){
	jQuery.fn.viewer = function(settings){
		
		settings = jQuery.extend({
			prevButtonSelector: '.viewer_prev',	// previous button selector
			nextButtonSelector: '.viewer_next',	// next button selector
			navButtonsSelector: '.viewer_nav', // circle buttons
			itemsSelector: '.viewer_items li', // the items for the viewer
			viewerMask: '.viewer_mask', // mask element selector,
			activeNavButtonClass: 'active', // active class for the nav items
			navButtonClass: 'nav_button', // active class for the nav items
			displayNum: 3, // number of items to display in the viewer area
			disableFadeTo: .2,
			showNumbers: true,
			createButtons: true,
			loop: false,
			imageClickAdvance: false,
			itemWidth: null,
			startIndex: 0
		}, settings);
		
		this.each(function(){
			var context = this;
			var $viewerPrev = $(settings.prevButtonSelector, context);
			var $viewerNext = $(settings.nextButtonSelector, context);
			var $viewerNav = $(settings.navButtonsSelector, context);
			var $viewerItems = $(settings.itemsSelector, context);
			var itemsWidth = 0;
			var itemWidth = (settings.itemWidth) ? settings.itemWidth : $(settings.itemsSelector + ':first', context).outerWidth(true);

			var displayNum = settings.displayNum;
			var maxNav = Math.ceil($viewerItems.size() / displayNum);
			var viewerWidth = $(settings.viewerMask, context).outerWidth();

			var currentNav = 0;
			var activeNav = null;
			var activeNavClass = settings.activeNavButtonClass;
			var activeMover = null;
			var maxNum = maxNav - 1;
			var minNum = 0;

			$viewerItems.each(function(i){
				itemsWidth += $(this).outerWidth(true);
			});
	
			$viewerItems.parent().width(itemsWidth);
	
			$viewerNav.children('.' + settings.navButtonClass).remove();
			if (maxNav > 1) {
				if (settings.createButtons){
					for (var i = 0; i < maxNav; i++){
						var html = '<li class="' + settings.navButtonClass + '"><a href="#">';
						if (settings.showNumbers) html += (i + 1);
						html += '</a>';
						$viewerNav.append(html);
					}

					$(settings.navButtonsSelector + ' .' + settings.navButtonClass, context).each(function(i){
						$(this).click(function(e){
							changeItems(i);
							return false;
						});
					});
				}
			} else {
				$(settings.prevButtonSelector, context).hide();
				$(settings.nextButtonSelector, context).hide();
			}
		
			var prevItem = function(){
				var tmpNav = currentNav - 1;
				if (tmpNav >= minNum){
					changeItems(tmpNav);
				} else if (settings.loop){
					changeItems(maxNum);
				}
			}
			$viewerPrev.click(function(){
				prevItem();
				return false;
			});
			
			
			var nextItem = function(){
				var tmpNav = currentNav + 1;
				if (tmpNav <= maxNum){
					changeItems(tmpNav);
				} else if (settings.loop){
					changeItems(0);
				}
			}
			
			$viewerNext.click(function(){
				nextItem();
				return false;
			});
			
			if (settings.imageClickAdvance){
				if (settings.loop){
					$viewerItems.css('cursor', 'pointer').click(function(){
						nextItem();
						return false;
					});
				} else {
					$viewerItems.slice(0, ($viewerItems.size() -1)).css('cursor', 'pointer').click(function(){
						nextItem();
						return false;
					});
				}
			}
	
			var disableMover = function($this){
				if (activeMover != $this){
					if ($this) $this.fadeTo('fast', settings.disableFadeTo).css('cursor', 'default');
					if (activeMover) activeMover.fadeTo('fast', 1).css('cursor', 'pointer');
				}
				activeMover = $this;
			}
			
			var changeItems = function(i){
				currentNav = i;
				var newX = currentNav * (itemWidth * displayNum);

				$viewerItems.parent().animate(
					{left: -newX}
				);

				if (settings.createButtons){
					var nav = $viewerNav.children('.' + settings.navButtonClass).eq(i);
					nav.addClass(activeNavClass);
					if (activeNav != null && activeNav != i) $viewerNav.children('.' + settings.navButtonClass).eq(activeNav).removeClass(activeNavClass);
					activeNav = i;
				}
				if (maxNav > 1 && !settings.loop){
					if (currentNav == minNum) {
						return disableMover($(settings.prevButtonSelector, context));
					} else if (currentNav == maxNum) {
						return disableMover($(settings.nextButtonSelector, context));
					} else if (activeMover){
						return disableMover(null);
					}
				}
				
			}
			changeItems(settings.startIndex);
		});
	}
})(jQuery);