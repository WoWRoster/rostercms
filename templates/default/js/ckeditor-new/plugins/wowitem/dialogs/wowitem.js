CKEDITOR.dialog.add( 'wowitem', function( editor ) {
	return {
		title: 'Add item by url',
		minWidth: 300,
		minHeight: 50,
		contents: [
			{
				id: 'item',
				elements: [
					{
						id: 'url',
						type: 'text',
						label: 'Url',
						width: '250px',
						// When setting up this field, set its value to the "align" value from widget data.
						// Note: Align values used in the widget need to be the same as those defined in the "items" array above.
						setup: function( widget ) {
							this.setValue( widget.data.url );
						},
						// When committing (saving) this field, set its value to the widget data.
						commit: function( widget ) {
							widget.setData( 'url', this.getValue() );
						}
					}
				]
			}
		]
	};
} );