/**
 * Basic sample plugin inserting abbreviation elements into CKEditor editing area.
 *
 * Created out of the CKEditor Plugin SDK:
 * http://docs.ckeditor.com/#!/guide/plugin_sdk_sample_1
 */

// Register the plugin within the editor.
CKEDITOR.plugins.add( 'fuellink', {

	// Register the icons.
	icons: 'anchor',

	// The plugin initialization logic goes inside this method.
	init: function( editor ) {
		editor.on( 'doubleclick', function( evt )
        {
           var element = evt.data.element;
           if ( element.is( 'a' ) && !element.getAttribute( '_cke_realelement' )){
				editor.execCommand('fuellink');
           }
           
        }, null,null,100);
		// Define an editor command that opens our dialog.
		editor.addCommand( 'fuellink', {
	        exec: function( editor ) {
				var selection = editor.getSelection();
				element = selection.getStartElement();
				var input, target, title, className, linkPdfs;
				if ( element ) {
					element = element.getAscendant( 'a', true );
					if (element){
						var href = element.getAttribute('href');
						var regex = "^" + myMarkItUpSettings.parserLeftDelimiter(true) + "site_url\('(.*)'\)" + myMarkItUpSettings.parserRightDelimiter(true);
						input = href.replace(new RegExp(regex, 'g'), function(match, contents, offset, s) {
		   										return contents;
	    								}
									);
						target = element.getAttribute('target');
						title = element.getAttribute('title');
						className = element.getAttribute('class');
					}
				}
				linkPdfs = editor.element.getAttribute('data-link_pdfs');
				linkFilter = editor.element.getAttribute('data-link_filter');

				var selected = selection.getSelectedText();
				var selectedElem = selection.getSelectedElement();
				if (selectedElem){
					selected = selectedElem.getOuterHtml();
				}
				myMarkItUpSettings.displayLinkEditWindow(selected, {input: input, title: title, target: target, className: className, linkPdfs:linkPdfs, linkFilter:linkFilter}, function(replace){
					editor.insertHtml(replace);
				})
	        }
    	});

		// Register selection change handler for the unlink button.
		 editor.on( 'selectionChange', function( evt )
			{
				/*
				 * Despite our initial hope, document.queryCommandEnabled() does not work
				 * for this in Firefox. So we must detect the state by element paths.
				 */
				var command = editor.getCommand( 'fuelunlink' ),
					element = evt.data.path.lastElement.getAscendant( 'a', true );
				if ( element && element.getName() == 'a' && element.getAttribute( 'href' ) )
					command.setState( CKEDITOR.TRISTATE_OFF );
				else
					command.setState( CKEDITOR.TRISTATE_DISABLED );
			} );


    	// Define an editor command that opens our dialog.
		editor.addCommand( 'fuelunlink', {
	        exec: function( editor ) {

	        	/*
				 * execCommand( 'unlink', ... ) in Firefox leaves behind <span> tags at where
				 * the <a> was, so again we have to remove the link ourselves. (See #430)
				 *
				 * TODO: Use the style system when it's complete. Let's use execCommand()
				 * as a stopgap solution for now.
				 */
				var selection = editor.getSelection(),
					bookmarks = selection.createBookmarks(),
					ranges = selection.getRanges(),
					rangeRoot,
					element;

				for ( var i = 0 ; i < ranges.length ; i++ )
				{
					rangeRoot = ranges[i].getCommonAncestor( true );
					element = rangeRoot.getAscendant( 'a', true );
					if ( !element )
						continue;
					ranges[i].selectNodeContents( element );
				}

				selection.selectRanges( ranges );
				editor.document.$.execCommand( 'unlink', false, null );
				selection.selectBookmarks( bookmarks );
	        	
	        }
    	});


		// // Create a toolbar button that executes the above command.
		editor.ui.addButton( 'FUELLink', {

		// 	// The text part of the button (if available) and tooptip.
			label: 'Insert Link',

		// 	// The command to execute on click.
			command: 'fuellink',

		// 	// The button placement in the toolbar (toolbar group name).
			toolbar: 'insert'
		});
		
		// // Create a toolbar button that executes the above command.
		editor.ui.addButton( 'FUELUnlink', {

		// 	// The text part of the button (if available) and tooptip.
			label: 'Remove Link',

		// 	// The command to execute on click.
			command: 'fuelunlink',

		// 	// The button placement in the toolbar (toolbar group name).
			toolbar: 'insert'
		});
	}
});

