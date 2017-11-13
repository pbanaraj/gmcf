(function($) {
	alert("kkkkkkkkkkkkkkk");
	editor.on('contentDom', function()
	{
		editor.document.on('keydown', function( event )
		{
			if ( !event.data.$.ctrlKey && !event.data.$.metaKey )
				alert("kjkjk");
		}); 
	});

})(jQuery);

	
