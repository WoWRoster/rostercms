CKEDITOR.plugins.add( 'wowitem', {
	// This plugin requires the Widgets System defined in the 'widget' plugin.
	requires: 'widget',

	// Register the icon used for the toolbar button. It must be the same
	// as the name of the widget.
	icons: 'wowitem',

	// The plugin initialization logic goes inside this method.
	init: function( editor ) {
		// Register the editing dialog.
		CKEDITOR.dialog.add( 'wowitem', this.path + 'dialogs/wowitem.js' );
		
		editor.widgets.add( 'simplebox', {
			
			template:	'[item]item_id[/item]',
			button: 'Create a wow item',
			dialog: 'wowitem',
			init: function() {
				var url = this.element.getStyle( 'url' );
				if ( url )
					this.setData( 'url', url );
			},
			data: function() {
				// Check whether "width" widget data is set and remove or set "width" CSS style.
				// The style is set on widget main element (div.simplebox).
				if ( this.data.url == '' )
					this.element.removeStyle( 'url' );
				else
					this.element.setStyle( 'url', this.data.url );

				// Brutally remove all align classes and set a new one if "align" widget data is set.
				this.element.removeClass( 'align-left' );
				this.element.removeClass( 'align-right' );
				this.element.removeClass( 'align-center' );
				if ( this.data.align )
					this.element.addClass( 'align-' + this.data.align );
			}
		} );
	}
} );