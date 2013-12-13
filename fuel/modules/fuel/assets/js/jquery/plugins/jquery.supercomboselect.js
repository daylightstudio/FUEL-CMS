// requires the jquery.fillin, the sortables jquery ui plugin and the selso sorting plugin and jquery.disable.text.select.pack

(function($){
	jQuery.fn.supercomboselect = function(settings){
		settings = jQuery.extend({
			addButton: ' &rarr; ',	// text of the "add" button
			removeButton: ' &larr; ',	// text of the "remove" button
			optionsIdPrefix : 'auto', // used to prevent id name collision
			wrapperId : 'auto', // id used to wrap the combo boxes
			selectedClass : 'selected', // name for the selected class for the lis
			isSortable : false, // allows for the right to be a sortable list by drag and drop
			isSearchable : 'auto', // allows for searching to appear... can be true, false or auto
			searchableAutoLimit : 10, // sets the number that the number of select options needs to have before the auto search text area kicks in.
			autoSort : true, // automatically sorts the right combo box alphabetically
			valuesEmptyString : 'There are no more values to select', // string to display when the there are no more values in the left
			selectedEmptyString : 'Select from the values on the left', // string to display when the there are no more values in the left
			defaultSearchBoxString : 'Filter your search', // string to display in the search text box...
			minNumOfSearchChars : 3, //... mmm variable name says it all
			selectedOrdering : [] // used for ordering the selected when you have something sorted and may not match the ordering on the left
		}, settings);

		this.each(function(i){

			/**********************************************************************
			SET UP VARIABLES
			**********************************************************************/

			// set up reference to current element
			var _this = this;


			// the id of the original element
			var selectID = this.id;

			// ids for the left and right sides
			// of the combo box we're creating
			var leftID = selectID + '_left';
			var rightID = selectID + '_right';

			// for keyboard shortcuts to select multiple
			var isShiftDown = false;
			var isCtrlDown = false;
			var lastSelected = null;

			// the form which contains the original element
			var theForm = $(this).parents('form');

			// place to store markup for the combo box
			var combo = '';

			// references to source options
			var refs;

			// reference to search box
			var searchBox = null;


			// create auto settings
			if (!settings.optionsIdPrefix || settings.optionsIdPrefix == 'auto' || settings.optionsIdPrefix.length === 0){
				settings.optionsIdPrefix = selectID + '_';
			}
			if (!settings.wrapperId || settings.wrapperId == 'auto' || settings.optionsIdPrefix.length === 0){
				settings.wrapperId = selectID + '_wrapper';
			}
			if (settings.isSearchable == 'auto'){
				if ($('#' + selectID).find('option').length >= settings.searchableAutoLimit) {
					settings.isSearchable = true;
				} else {
					settings.isSearchable = false;
				}
			}

			var customSelectedSorting = (settings.isSortable && settings.selectedOrdering);


			/**********************************************************************
			SET HTML
			**********************************************************************/

			$('#' + settings.wrapperId).remove();

			// create wrapper div
			//$(this).after('<div id="' + settings.wrapperId + '" class="supercomboselect_wrapper"></div>');

			// build the combo box
			combo += '<div id="' + settings.wrapperId + '" class="supercomboselect_wrapper">';
			combo += '<div class="supercomboselect_left">';
			combo += '<div class="supercomboselect">';
			combo += '<div class="supercomboselect_empty_msg supercomboselect_left_empty_msg">' + settings.valuesEmptyString + '</div>';
			combo += '<ul id="' + leftID + '" class="supercomboselect_list">';
			combo += '</ul>';
			combo += '</div>';
			combo += '</div>';
			combo += '<div class="supercomboselect_btns">';
			combo += '<input type="button" class="csadd" value="' + settings.addButton + '" />';
			combo += '<input type="button" class="csremove" value="' + settings.removeButton + '" />';
			combo += '</div>';
			combo += '<div class="supercomboselect_right">';
			combo += '<div class="supercomboselect">';
			combo += '<div class="supercomboselect_empty_msg supercomboselect_right_empty_msg">' + settings.selectedEmptyString + '</div>';
			combo += '<ul id="' + rightID + '" class="supercomboselect_list">';
			combo += '</ul>';
			combo += '</div>';
			combo += '<div style="clear: both; height: 0px; line-height: 0px; font-size: 0px;"></div>'; // clears any floats
			combo += '</div>';
			combo += '</div>';

			// hide the original element and
			// add the combo box after it
			$(this).hide().after(combo);

			/**********************************************************************
			BIND EVENTS
			**********************************************************************/
			$('#' + leftID + ' li').die();
			$('#' + rightID + ' li').die();

			$('#' + leftID + ' li[class!="option_disabled"]').live('dblclick', function(e){
				addSelectedToRight();
			});

			$(document).on('dblclick', '#' + rightID + ' li', function(e){
				removeSelectedFromRight();
			});


			$('#' + leftID + ' li').live('click', function(e){
				if ($(this).hasClass('optgrp') === false)
				{
					if (isCtrlDown) {
						$(this).removeClass(settings.selectedClass);
					} else {
						markForMove(this, leftID);
					}
				}
			});


			$(document).on('click', '#' + rightID + ' li', function(e){
				if (isCtrlDown) {
					$(this).removeClass(settings.selectedClass);
				} else {
					markForMove(this, rightID);
				}
			});

			$(document).keydown(function(e){
				if (e.shiftKey) {
					isShiftDown = true;
				//} else if (e.metaKey || e.keyCode == 224 || e.keyCode == 91 || e.keyCode == 93){ // Safari/Chrome right and left command keys are 91 and 93. Firefox is 224
				} else if (e.metaKey || e.CtrlKey){
					isCtrlDown = true;
				} else if (e.keyCode == 38) {
					if (lastSelected) markForMove($(lastSelected).prev());
				} else if (e.keyCode == 40) {
					if (lastSelected) markForMove($(lastSelected).next());
				}
			});

			$(document).keyup(function(e){
				isShiftDown = false;
				isCtrlDown = false;
			});

			$('#' + settings.wrapperId + ' .csadd').click(function(){
				addSelectedToRight();
			});

			$('#' + settings.wrapperId + ' .csremove').click(function(){
				removeSelectedFromRight();
			});

			$('#' + settings.wrapperId + ' .supercomboselect_left_empty_msg, #' + settings.wrapperId + ' .supercomboselect_right_empty_msg').hide();


			if (settings.isSortable){
				$('#' + rightID).sortable();
			}

			// if the object is sortable, then we need to create hidden fields instead and remove the mult-select form field to allow for ordering
			if (settings.isSortable){
				theForm.submit(function(){
					onFormSubmit();
					return true;
				});
			}

			// event for ajaxSubmit
			theForm.bind('form-pre-serialize', function(){
				onFormSubmit();
			});



			/**********************************************************************
			SEARCH BOX SETUP
			**********************************************************************/
			if (settings.isSearchable){
				var prevSearchText = '';
				var nonSearchableChars = [13, 37, 38, 39, 40, 18, 17]; //return , arrows, alt, option ctrl
				var searchBoxHTML = '<div class="supercomboselect_search">';
				searchBoxHTML += '<input type="text" value="" name="supercomboselect_search" class="supercomboselect_search_text" /> <a href="#" class="supercomboselect_search_clear" style="display: none;">Clear Filter</a>';
				searchBoxHTML += '</div>';

				$('#' + leftID).parent().after(searchBoxHTML);
				searchBox = $('#' + leftID).parent().parent().find('.supercomboselect_search_text');
				searchBox
					.attr('placeholder', settings.defaultSearchBoxString)
					.keyup(function(e){
						isShiftDown = false; // reset this to prevent issue with not being able to select
						if (e.shiftKey || e.metaKey || e.altKey || e.ctrlKey || $.inArray(e.keyCode, nonSearchableChars) != -1) return false;
						var searchTerm = searchBox.val(),
							$searchClear = searchBox.siblings('.supercomboselect_search_clear');
						$searchClear.hide();
						if (searchTerm.length) {
							$searchClear.show();
						}
						if (searchTerm.length >= settings.minNumOfSearchChars || searchTerm.length === 0) {
							// only refresh list when we know that the current search term doesn't begin with the previous search term
							if (searchTerm.substr(0, (prevSearchText.length)) != prevSearchText || searchTerm.length === 0) {
								// commented out because this can be slow with big lists
								refreshLists();
							}
							var val = $(this).val().toLowerCase();
							if (val.length){
								var filtered = $('#' + leftID + ' li:not(.optgrp)').filter(function(){
									var text = $(this).data('label');
									if (!text){
										return false;
									}
									var index = text.toLowerCase().indexOf(val);
									if (index == -1){
										return false;
									} else {
										var highlightedText = '';
										highlightedText += text.substr(0, index);
										highlightedText += '<span class="supercomboselect_search_highlight">' + text.substr(index, val.length) + '</span>';
										highlightedText += text.substr((index + val.length));
										$(this).html(highlightedText);
										return true;
									}

								});
								$('#' + leftID).empty().append(filtered);
							}

						// refresh list if someone is deleting... but only do it once if it is below the minNumSearchChars
						} else if (searchTerm.length == (settings.minNumOfSearchChars - 1) && searchTerm.substr(0, (prevSearchText.length)) != prevSearchText && searchTerm.length !== 0){
							refreshLists();
						}
						prevSearchText = searchTerm;


						return false;
					}).keydown(function(e){
						// return character
						if (e.keyCode == 13) {
							$(e.currentTarget).blur();
							return false;
						}
					});
				searchBox.siblings('.supercomboselect_search_clear').click(function(e){
					e.preventDefault();
					refreshLists();
					searchBox.val('');
					$(this).hide();
				});
			}



			/**********************************************************************
			FUNCTIONS
			**********************************************************************/

			function refreshLists(init){
				var leftOpts = [];
				var rightOpts = [];
				refs = []; // reset arrays
				var idNum = 0;

				var maxSelected = (typeof settings.selectedOrdering == 'boolean') ? 0 : settings.selectedOrdering.length;
				var flippedSelectedOrder = {};
				if (customSelectedSorting && typeof settings.selectedOrdering == 'object'){
					for(var n in settings.selectedOrdering){
						flippedSelectedOrder[settings.selectedOrdering[n]] = n;
					}
				}
				var $select = $('#' + selectID),
					$optgroup = $select.find('optgroup');

				$select.find('option').each(function(i){

					var text = $(this).text(),
						value = $(this).attr('value'),
						id = (settings.optionsIdPrefix + idNum),
						opt = '',
						$opt = $('<li/>');

					$opt.text(text);
					$opt.attr('id', id);
					$opt.attr('data-label', text);
					if ($(this).is(':disabled')) {
						$opt.addClass('option_disabled');
					}
					if (customSelectedSorting)
					{
						var optSpanVal = (flippedSelectedOrder[value]) ? flippedSelectedOrder[value] : maxSelected;
						$opt.append('<span/>');
						$opt.find('span').attr('id', id + '_val').text(optSpanVal).hide();
					}

					opt = $opt[0].outerHTML;

					if ( !$(this).is('[selected]') && $(this).parent().is('optgroup') && ($(this).parent().find('option:not([selected])')[0] === $(this)[0]) )
					{
						opt = '<li class="optgrp">' + $(this).parent().attr('label') + '</li>' + opt;
					}

					if ($(this).attr('selected')){
						rightOpts.push(opt);
					} else {
						leftOpts.push(opt);
					}
					refs[idNum] = this; // create lookup array for all elements associated with an idNum

					idNum++;
				});

				leftOpts = leftOpts.join("\n");
				rightOpts = rightOpts.join("\n");

				theForm.find('#' + leftID).empty().append(leftOpts);
				if (!customSelectedSorting || init) theForm.find('#' + rightID).empty().append(rightOpts);

				// set left and right messages
				$('#' + settings.wrapperId + ' .supercomboselect_left_empty_msg').hide();
				$('#' + settings.wrapperId + ' .supercomboselect_right_empty_msg').hide();
				if (!$('#' + leftID).children().length){
					$('#' + settings.wrapperId + ' .supercomboselect_left_empty_msg').show();
				}
				if (!$('#' + rightID).children().length){
					$('#' + settings.wrapperId + ' .supercomboselect_right_empty_msg').show();
				}

				// set this to false just in case the event doesn't get triggered
				isShiftDown = false;
				isCtrlDown = false;

				// IE hack to get overflow to expand the full width after scrollbars appear
				$('.supercomboselect').css({overflowY:'hidden'}).css({overflowY:'auto'}).disableTextSelect();

				$('#form').trigger('supercombo_list_refreshed');

				autoSort();

			}

			function addSelectedToRight(){
				$selectedOpts = $('#' + leftID + ' li[class=' + settings.selectedClass + ']');
				var selected = [];
				$selectedOpts.each(function(i){
					if (customSelectedSorting) theForm.find('#' + rightID).append($(this).removeClass(settings.selectedClass).html($(this).html()));
					var opt = $(getOptionSourceRef(getIdNum(this)));
					opt.attr('selected', 'selected');
					selected.push(opt.attr('value'));
				});

				$(_this).trigger('selectionAdded', [selected]);

				// reset search box area and refresh the list
				if (searchBox && searchBox.val().length){
					searchBox.val('');
					searchBox.focus();
				}
				refreshLists();
			}

			function removeSelectedFromRight(){
				$selectedOpts = $('#' + rightID + ' li[class=' + settings.selectedClass + ']');
				var selected = [];
				$selectedOpts.each(function(i){
					if (customSelectedSorting) $(this).remove();
					var opt = $(getOptionSourceRef(getIdNum(this)));
					opt.removeAttr('selected', 'selected');
					selected.push(opt.attr('value'));
				});

				$(_this).trigger('selectionRemoved', [selected]);
				refreshLists();
			}

			function markForMove(selector){
				if (isShiftDown && lastSelected){
					var lis = $(selector).parents('ul').find('li');
					var num1 = lis.index($(selector));
					var num2 = lis.index($(lastSelected));
					if (num1 < num2){
						startNum = num1;
						endNum = num2;
					} else {
						startNum = num2;
						endNum = num1;
					}
					if (startNum > -1){
						lis.slice(startNum, (endNum + 1)).addClass(settings.selectedClass);
					}
				} else {
					$(selector).addClass(settings.selectedClass);
					lastSelected = selector;
				}

				// unfocus this field so that meta key tags will send proper events
				if (searchBox){
					searchBox.blur();
				}

			}

			function getOptionSourceRef(idNum){
				return refs[idNum];
			}

			function getIdNum(selector){
				var id = $(selector).attr('id');
				var idNum = (id) ? parseInt(id.substr(settings.optionsIdPrefix.length), 10) : 0;
				return idNum;
			}

			// sort the boxes and clear highlighted items
			function autoSort(){
				if (settings.autoSort && !settings.isSortable){
					var beginIndex = settings.optionsIdPrefix.length;
					$('#' + rightID).find('li').selso({
						type: 'alpha',
						extract: function(o){
							return $(o).text().toLowerCase();
						}
					});
				}
			}

			function onFormSubmit(){
				$('#' + rightID + ' li').each(function(i){
					var src = $(getOptionSourceRef(getIdNum(this)));
					$(this).prepend('<input type="hidden" name="' + $('#' + selectID).attr('name') + '" value="' + src.attr('value') + '"" class="sorted_val" />');
				});
				$('#' + selectID).remove();
			}

			// initiate lists
			refreshLists(true);

			if (customSelectedSorting){
				$('#' + rightID).find('li').selso({
					type: 'num',
					orderBy: 'span',
					direction: 'asc'

				});
			}
		});

		return this;
	};
})(jQuery);
