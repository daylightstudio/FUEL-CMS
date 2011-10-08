/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
   //var ckpath = 'http://dev.altadisblog.com/fuel/modules/fuel/assets/js/editors/ckeditor/';
    // var ckpath = 'http://localhost/daylight/wisegroup/te_pou/repo/fuel/modules/fuel/assets/js/editors/ckeditor/';
    // config.filebrowserBrowseUrl = ckpath + 'filemanager/index.html';
    // config.filebrowserImageBrowseUrl = ckpath + 'filemanager/index.html?type=Images';
    // config.filebrowserFlashBrowseUrl = ckpath + 'filemanager/index.html?type=Flash';
    // config.filebrowserUploadUrl = null;
    // config.filebrowserImageUploadUrl = null;
    // config.filebrowserFlashUploadUrl = null;
    // config.enterMode = CKEDITOR.ENTER_BR;
    // config.shiftEnterMode = CKEDITOR.ENTER_BR;
    // config.protectedSource.push( /<\?[\s\S]*?\?>/g );
	
	/* Moved here as apprently doesn't work in MY_fuel */
	config.enterMode = CKEDITOR.ENTER_P;
	config.shiftEnterMode = CKEDITOR.ENTER_BR;
	
	config.protectedSource.push( /\{[\s\S]*?\}/gi );
	config.protectedSource.push( /<\?[\s\S]*?\?>/g );
};