
jQuery(document).ready(function($){

		$ = jQuery.noConflict();
	
		var toshow = 'title'; 		

		$('.lshowcase-tooltip').tooltip({
		content: function () { return $(this).attr(toshow) },
		position: {
		my: "center bottom-20",
		at: "center top",
		using: function( position, feedback ) {
		$( this ).css( position );
		$( "<div>" )
		.addClass( "lsarrow" )
		.addClass( feedback.vertical )
		.addClass( feedback.horizontal )
		.appendTo( this );
		}
		}
		});
	

});
