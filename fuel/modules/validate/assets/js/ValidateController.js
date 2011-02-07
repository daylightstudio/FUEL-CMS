var ValidateController = {
	
	init : function(initObj){
		this._notifications();
		this._submit();
		this._super(initObj);
	},
	
	validate : function(){
		this._notifications();
		$('.fillin').fillin(this.localized.validate_pages_input);
		var _this = this;
		$('#submit_html').click(function(){
			$('.csadd').click();
			$('#form').submit();
			return false;
		})

		$('#submit_links').click(function(){
			$('.csadd').click();
			$('#form').attr('action', _this.modulePath + '/validate/links').submit();
			return false;
		});
		
		$('#submit_size_report').click(function(){
			$('.csadd').click();
			$('#form').attr('action', _this.modulePath + '/validate/size_report').submit();
			return false;
		})
		var comboOpts = this._comboOps('#pages');
		$('#pages').supercomboselect(comboOpts);
	},
	
	html : function(){
		var pages = this.initObj.pages;
		var i = 1;
		var totalToLoad = pages.length;
		var totalValid = 0;
		var totalInvalid = 0;
		var _this = this;

		this.displayProcessingText(i, totalToLoad);
		
		for (var n in pages){
			var uri = pages[n];
			(function (u) {
			  	$.post(_this.modulePath + '/validate/html', { uri : u }, 
					function(html){
						_this.displayProcessingText(i, totalToLoad);
						if (_this.processHtmlResults(i, html, u)){
							totalValid++;
						} else {
							totalInvalid++;
						}

						$('#summary_' + i).click(function(){
							$(this).parent().find('.result').slideToggle();
						});
						if (totalToLoad == i){
							var completedText = '<h2>' + _this.lang('validate_total_valid') + ': <span class="success">' + totalValid + '</span> &nbsp; &nbsp; ' + _this.lang('validate_total_invalid') + ': <span class="error">' + totalInvalid + '</span></h2>';
							$('#validation_status_text').html(completedText);
							$('#validation_status .loader').hide();
						}
						
						(function (num, uri) {
							$('#results_refresh_' + num).click(function(e){
								var $this = $(this)
								$this.addClass('loader_sm');
								$.post(_this.modulePath + '/validate/html', { uri : uri }, 
									function(html){
										_this.processHtmlResults(num, html, u);
										$this.removeClass('loader_sm');
									});
							})
						})(i, u)
						
						i++;
					});
			})(uri);
		}
	},
	
	processHtmlResults : function(i, html, u){
		var _this = this;
		var doc = _this.createResultsIframe(i, html);

		var summary = '<span class="uri">' + u + '</span> &nbsp; ';
		var $invalid = $('.invalid', doc);
		var $invalidText = $('td.invalid', doc);
		var edit_url = $('#edit_url', doc).text();
		
		if (edit_url.length > 0){
			$('#edit_' + i).html('<a href="' + edit_url + '">' + _this.lang('btn_edit') + '</a>');
		}
		if ($invalid.size() > 0 ) {
			var invalidStr = ($.trim($invalidText.text()).length) ? $invalidText.html() : $('#results', doc).html();
			summary += '<span class="error">' + invalidStr + '</span>';
			$('#summary_' + i).html(summary);
			return false;
		} else{
			summary += '<span class="success">' + _this.lang('validate_valid') + '</span>';
			$('#summary_' + i).html(summary);
			return true;
		}
	},
	
	links : function(){
		var pages = this.initObj.pages;
		var i = 1;
		var totalToLoad = pages.length;
		var totalValid = 0;
		var totalInvalid = 0;
		var _this = this;
		
		this.displayProcessingText(i, totalToLoad);
		
		for (var n in pages){
			var uri = pages[n];

			(function (u) {
			  	$.post(_this.modulePath + '/validate/links', { uri : u }, 
					function(html){
						_this.displayProcessingText(i, totalToLoad);
						
						if (_this.processLinksResults(i, html, u)){
							totalValid++;
						} else {
							totalInvalid++;
						}

						$('#summary_' + i).click(function(){
							$(this).parent().find('.result').slideToggle();
						});
						if (totalToLoad == i){
							var completedText = '<h2>' + _this.lang('validate_total_valid') + ': <span class="success">' + totalValid + '</span> &nbsp; &nbsp; ' + _this.lang('validate_total_invalid') + ': <span class="error">' + totalInvalid + '</span></h2>';
							$('#validation_status').html(completedText);
							$('#validation_status .loader').hide();
						}
						
						(function (num, uri) {
							$('#results_refresh_' + num).click(function(e){
								var $this = $(this)
								$this.addClass('loader_sm');
								$.post(_this.modulePath + '/validate/links', { uri : uri }, 
									function(html){
										_this.processLinksResults(num, html, u);
										$this.removeClass('loader_sm');
									});
							})
						})(i, u)
						
						i++;
					});
			})(uri);
		}
	},
	
	processLinksResults : function(i, html, u){
		var _this = this;
		var doc = _this.createResultsIframe(i, html);
		
		var summary = '<span class="uri">' + u + '</span> &nbsp; ';
		var invalid_text = $('#total_invalid', doc).text();
		var invalid_num = parseInt($('#total_invalid_num', doc).text());
		var edit_url = $('#edit_url', doc).text();
		
		if (edit_url.length > 0){
			$('#edit_' + i).html('<a href="' + edit_url + '">' + _this.lang('btn_edit') + '</a>');
		}
		
		
		if (invalid_num > 0){
			summary += '<span class="error">' + invalid_text + '</span>';
			$('#summary_' + i).html(summary);
			return false;
		} else {
			summary += '<span class="success">' + _this.lang('validate_all_links_valid') + '</span>';
			$('#summary_' + i).html(summary);
			return true;
		}

	},
	
	size : function(){
		var pages = this.initObj.pages;
		var i = 1;
		var totalToLoad = pages.length;
		var totalValid = 0;
		var totalInvalid = 0;
		var _this = this;
		
		this.displayProcessingText(i, totalToLoad);
		
		for (var n in pages){
			var uri = pages[n];

			(function (u) {
			  	$.post(_this.modulePath + '/validate/size_report', { uri : u }, 
					function(html){
						_this.displayProcessingText(i, totalToLoad);
						_this.processSizeResults(i, html, u);
						$('#summary_' + i).click(function(){
							$(this).parent().find('.result').slideToggle();
						});
						if (totalToLoad == i){
							var completedText = '<h2>' + _this.lang('validate_processing_complete') + '</h2>';
							$('#validation_status').html(completedText);
							$('#validation_status .loader').hide();
						}
						
						(function (num, uri) {
							$('#results_refresh_' + num).click(function(e){
								var $this = $(this)
								$this.addClass('loader_sm');
								$.post(_this.modulePath + '/validate/size_report', { uri : uri }, 
									function(html){
										_this.processSizeResults(num, html, u);
										$this.removeClass('loader_sm');
									});
							})
						})(i, u)
						
						i++;
					});
			})(uri);
		}
	},
	
	processSizeResults : function(i, html, u){
		var _this = this;
		var doc = _this.createResultsIframe(i, html);
		
		var summary = '<span class="uri">' + u + '</span> &nbsp; ';
		if ($('#total_error', doc).text().substr(0, 1) === '-'){
			summary += '<span class="error">' + _this.lang('validate_error_reading_files') + '</span> ';
		} else {
			var total_error = parseInt($('#total_error .num', doc).text());
			var total_warn = $('#total_warn', doc).text();
			var total_ok = $('#total_ok', doc).text();
			if (total_error) summary += '<span class="error">' + total_error + '</span> ';
			if (total_warn) summary += '<span class="warning">' + total_warn + '</span> ';
			if (total_ok) summary += '<span class="success">' + total_ok + '</span> ';
		}
		$('#summary_' + i).html(summary);

		return true;

	},
	
	
	displayProcessingText : function(num, totalToLoad){
		$('#validation_status_text').html(this.lang('validate_processing') + ' ' + num + ' of ' + totalToLoad);
	},
	
	createResultsIframe : function(i, html){
		var iframe = document.createElement( "iframe" );
		iframe.setAttribute('id', 'iframe_' + i);
		iframe.setAttribute('name', 'iframe_' + i);
		iframe.setAttribute('frameborder', 0);
		
		// if node already exists... then we replace instead of append
		if (!$('#validation_' + i).size() > 0){
			$('#validation_results').append('<div id="validation_' + i + '" class="validation"><a href="#" class="results_refresh" id="results_refresh_' + i + '"></a><div id="summary_' + i + '" class="summary"></div><div id="edit_' + i+ '" class="edit"></div><div id="result_' + i + '" class="result"></div>')
		}
		$('#result_' + i).html(iframe);	
		var doc = iframe.contentDocument || iframe.contentWindow.document;
		doc.open("text/html", false);
		doc.write(html);
		doc.close();
		return doc;
	},
	
	reloadResults : function(i){
		var _this = this;
		var u = $('#summary_' + i + ' .uri').html();
		$.post(_this.modulePath + '/validate/html', { uri : u }, 
			function(html){
				$('#result_' + i).empty();
				var doc = _this.createResultsIframe(i, html);
				var summary = '<span class="uri">' + u + '</span> &nbsp; ';
				var invalid_text = $('#total_invalid', doc).text();
				console.log(invalid_text)
				var invalid_num = parseInt($('#total_invalid_num', doc).text());

				if (invalid_num > 0){
					summary += '<span class="error">' + invalid_text + '</span>';
				} else {
					summary += '<span class="success">' + _this.lang('validate_all_links_valid') + '</span>';
				}

				$('#summary_' + i).html(summary);
			});
	}
	
	
};
jqx.extendController(ValidateController);
