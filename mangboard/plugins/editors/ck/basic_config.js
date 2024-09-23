/**
 * @license Copyright (c) 2003-2020, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbarGroups = [
		{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },		
		{ name: 'insert' },
		{ name: 'forms' },
		{ name: 'tools' },
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'links' },
		{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },		
		{ name: 'document',	   groups: [ 'document', 'mode' ] },
		{ name: 'others' },		
		'/',		
		{ name: 'styles' },
		{ name: 'colors' },
		
		{ name: 'paragraph',   groups: [ 'align', 'list', 'indent'] },
		{ name: 'about' }
	];
	config.height = '260px';

	config.removePlugins = 'print,save,bidi,blocks,forms,flash,iframe,pagebreak,about,maximize,showblocks,newpage,language,find,exportpdf';
	config.removeButtons = 'Styles,ExportPdf,Preview,Copy,Format,Cut,Paste,Print,SelectAll,CreateDiv,Anchor,PasteText,PasteFromWord,Select,HiddenField,Underline,Subscript,Superscript,Scayt';

	// Simplify the dialog windows.
	config.removeDialogTabs = 'image:advanced;link:advanced';

	config.extraAllowedContent = 'img[src,alt,width,height]';
	config.allowedContent = true;
	config.versionCheck	 = false;
	config.clipboard_handleImages	= false;
};
