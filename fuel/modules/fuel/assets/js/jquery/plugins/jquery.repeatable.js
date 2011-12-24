/*
(c) Copyrights 2011

Author David McReynolds
Daylight Studio
dave@thedaylightstudio.com
*/

;(function($){
	jQuery.fn.repeatable = function(options) {
		options = $.extend({
			addButtonText : 'Add Another',
			addButtonClass : 'add_another',
			removeButtonClass : 'remove',
			removeButtonText : 'Remove',
			repeatableSelector : '.repeatable',
			warnBeforeDelete : true,
			warnBeforeDeleteMessage : 'Are you sure you want to delete this item?',
			sortableSelector : 'h4',
			sortable : true,
			limit : null
		}, options || {});
		
		var parseTemplate = function(elem, i){
			$('.num', elem).html((i + 1));
			$('.index', elem).html(i);
			$('input,textarea,select', elem).each(function(j){
				var newName = $(this).attr('name')
				if (newName.length){
					newName = newName.replace(/([-_a-zA-Z0-9]+)\[\d\]/g, '$1[' + i + ']');
					$(this).attr('name', newName);
				}

				var newId = $(this).attr('id');
				if (newId.length){
					newId = newId.replace('{index}', i);
					$(this).attr('id', newId);
				}
			})
		}
		
		var addRemove = function(elem, i){
			$(elem).append('<a href="#" class="' + options.removeButtonClass +'">' + options.removeButtonText +' </a>');
		}
		
		var reOrder = function($elem){
			$elem.children(options.repeatableSelector).each(function(i){
				parseTemplate(this, i);
			});
		}
		
		return this.each(function(){
			var $this = $(this);
			
			// simply return if it's already been instantiated
			if ($this.hasClass('__applied__')) return this;
			
			// set this class so we can detect whether it's been cloned yet or not
			$this.addClass('__applied__');
			
			// create clone to duplicate later
			$clone = $this.find(options.repeatableSelector + ':last').clone(false);

			// parse the template
			var $repeatables = $this.children(options.repeatableSelector);
			$repeatables.each(function(i){
				parseTemplate(this, i);
				addRemove(this, i);
			});
			
			// add button
			$parent = $this.parent();
			if ($parent.find(options.addButtonClass).size() == 0){
				$parent.append('<a href="#" class="' + options.addButtonClass + '">' + options.addButtonText +' </a>');
			}
			// add sorting
			if (options.sortable){
				$this.sortable({
					cursor: 'move', 
					handle: options.sortableSelector,
					start: function(event, ui) {
						$this.trigger({type: 'sortStarted', clonedNode: $this});
					},
					stop: function(event, ui) {
						reOrder($(this));
						$this.trigger({type: 'sortStopped', clonedNode: $this});
					}
				});
			}
			
			// set button handler
			$('.' + options.addButtonClass, $this.parent()).click(function(e){
				var $this = $(this).prev();
				
				// create clone of a clean clone
				$clonecopy = $clone.clone(false);
				var $children = $this.children(options.repeatableSelector);
				if (options.limit != null && $children.size() >= options.limit){
					return false;
				}
				var index = $children.size();
				parseTemplate($clonecopy, index);
				addRemove($clonecopy, index);
				$this.append($clonecopy);
				
				// remove values from any form fields
				$clonecopy.find('input,text,select,textarea').val('');
				
				$this.trigger({type: 'cloned', clonedNode: $clonecopy});
				return false;
			});

			// set button handler
			$('.' + options.removeButtonClass).live('click', function(e){
				var $this = $(this).parents(options.repeatableSelector).parent();
				if (options.warnBeforeDelete == false || confirm(options.warnBeforeDeleteMessage)){
					$(this).parent().remove();

					// to reorder the indexes
					reOrder($this);
				}
				$this.trigger('removed');
				return false;
			});
			return this;
		});
	};
})(jQuery);
