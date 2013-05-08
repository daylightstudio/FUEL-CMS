/**
 * Basic sample plugin inserting abbreviation elements into CKEditor editing area.
 *
 * Created out of the CKEditor Plugin SDK:
 * http://docs.ckeditor.com/#!/guide/plugin_sdk_sample_1
 */

// Register the plugin within the editor.
CKEDITOR.plugins.add( 'fuelimage', {

	// Register the icons.
	icons: 'anchor',

	// The plugin initialization logic goes inside this method.
	init: function( editor ) {
		editor.on( 'doubleclick', function( evt )
        {
           var element = evt.data.element;
           if ( element.is( 'img' ) && !element.getAttribute( '_cke_realelement' )){
				editor.execCommand('fuelimage');
           }
           
        }, null,null,100);
		// Define an editor command that opens our dialog.
		editor.addCommand( 'fuelimage', {
	        exec: function( editor ) {
				var selection = editor.getSelection();
				element = selection.getStartElement();

				var img = '', width, height, alt, align, className, imgFolder, imgOrder;
				if ( element ) {
					element = element.getAscendant( 'img', true );
					if (element){
						var src = element.getAttribute('src');

						element.setAttribute('data-cke-saved-src', src);

						// remove the img_path
						img = src.replace(/^\{img_path\('(.+)'\)\}/, function(match, contents, offset, s) {
		   										return contents;
	    								}
									);

						// remove the web path
						img = img.replace(jqx.config.assetsImgPath, '');

						width = element.getAttribute('width');
						height = element.getAttribute('height');
						alt = element.getAttribute('alt');
						align = element.getAttribute('align');
					}
				}
				imgFolder = editor.element.getAttribute('data-img_folder');
				imgOrder = editor.element.getAttribute('data-img_order');
				myMarkItUpSettings.displayAssetInsert(img, {width: width, height: height, alt: alt, align: align, className: className, imgFolder: imgFolder, imgOrder: imgOrder}, function(imgHtml){
					imgHtml = imgHtml.replace(/\{img_path\('(.+)'\)\}/g, function(match, contents, offset, s) {
		   										var img = jqx.config.assetsImgPath + contents;
		   										return img;
	    								}
									);
					editor.insertHtml(imgHtml);
				});
				
	        }
    	});

		// // Create a toolbar button that executes the above command.
		editor.ui.addButton( 'Image', {

		// 	// The text part of the button (if available) and tooptip.
			label: 'Insert an Image',

		// 	// The command to execute on click.
			command: 'fuelimage',

		// 	// The button placement in the toolbar (toolbar group name).
			toolbar: 'image'
		});
	}
});

