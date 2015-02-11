<style>
	#ignitewoo_event_product_data #custom-recurrence-weeks label { width: auto; display: block; float: left; margin-bottom: 3px;}
	#ignitewoo_event_product_data #custom-recurrence-frequency input { width: 30px; }
	#ignitewoo_event_product_data #custom-recurrence-years label { width: 50px; display: block; float: left; margin-bottom: 3px;}
	#ignitewoo_event_product_data #recurrence-changed-row { color: red; display: none; }
	#ignitewoo_event_product_data #rec-end-error { color: red; }
	#ignitewoo_event_product_data #rec-days-error { color: red; }
	#ignitewoo_event_product_data .rec-error { display:none; }
	#ignitewoo_event_product_data #ignitewoo_single_session_wrap { margin-left: 12px; }
	#ignitewoo_event_product_data #ignitewoo_multi_session_wrap { margin-left: 12px; border: 1px dotted #ccc; padding: 10px; }
	#ignitewoo_event_product_data  .event_form_speaker_wrap { margin-left: 12px; } 
	#ignitewoo_event_product_data .speaker { background-color: #EFEFEF; margin-bottom: 12px; }
	#ignitewoo_event_product_data .speaker .form-field { margin-left: 15px; }
	#ignitewoo_event_product_data img.help_tip {
		margin-top: -4px;
		width: 16px;
	}
	.ignitewoo_events_colorpicker  {
		background: none repeat scroll 0 0 #efefef;
		border: 1px solid #CCCCCC;
		display: none;
		position: absolute;
		z-index: 100;
	}
</style>

<script>

	<?php if ( !empty( $_GET['page'] ) && 'ignitewoo_events_settings' == $_GET['page'] ) { ?>

	jQuery( document ).ready( function() { 

		jQuery( '#colorpickerdiv_event_bg_color' ).hide();

		jQuery( '#colorpickerdiv_event_bg_color' ).farbtastic( '#event_bg_color_code' );

		jQuery( '#event_bg_color_code' ).click( function() {
		
			jQuery( '#colorpickerdiv_event_bg_color' ).fadeIn();
			
		});

		jQuery( '#colorpickerdiv_event_fg_color' ).hide();

		jQuery( '#colorpickerdiv_event_fg_color' ).farbtastic( '#event_fg_color_code' );

		jQuery( '#event_fg_color_code' ).click(function() {
		
			jQuery( '#colorpickerdiv_event_fg_color' ).fadeIn();
			
		});

		jQuery( document ).mousedown(function() {
		
			jQuery( '#colorpickerdiv_event_bg_color' ).each(function() {
			
				var display = jQuery( this ).css( 'display' );
				
				if ( 'block' == display )
					jQuery( this ).fadeOut();
					
			});
			
			jQuery( '#colorpickerdiv_event_fg_color' ).each(function() {
			
				var display = jQuery( this ).css( 'display' );
				
				if ( 'block' == display )
					jQuery( this ).fadeOut();
					
			});
		});

	});

	<?php } ?>

<?php 
	global $typenow; 
	
	if ( ( 'product' == $typenow || 'ignitewoo_event' == $typenow ) && !isset( $_GET['taxonomy'] ) ) {  
?>
	jQuery( document ).ready( function() { 

		if ( jQuery( '[name="is_recurring"]' ).val() == "true" && !jQuery( '[name="recurrence_action"]' ).val() ) {
		
			function recurrence_changed() {
			
				jQuery( '#recurrence-changed-row' ).show();
				
				jQuery( '[name="recurrence_action"]' ).val(2);
				
			}

			jQuery( '.recurrence-row input, .custom-recurrence-row input,.recurrence-row select, .custom-recurrence-row select' ).change( recurrence_changed )
			
			jQuery( '[name="ignitewoo_event_info[recurrence][end]"]' ).datepicker( 'option', 'onSelect', recurrence_changed );
		}
		
		jQuery( '[name="ignitewoo_event_info[recurrence][end]"]' ).datepicker( 'option', 'onSelect', function() {
		
			jQuery( '[name="ignitewoo_event_info[recurrence][end]"]' ).removeClass( 'placeholder' );
			
		});	
		

		jQuery( '[name="ignitewoo_event_info[recurrence][type]"]' ).change(function() {
		
			var current_option =  jQuery( this ).find( 'option:selected' ).val();
			
			jQuery( '.custom-recurrence-row' ).hide();

			if ( current_option == 'Custom' ) {
			
				jQuery( '#recurrence-end' ).show();
				
				jQuery( '#custom-recurrence-frequency' ).show();
				
				jQuery( '[name="ignitewoo_event_info[recurrence][custom-type]"]' ).change();
				
				jQuery( '#event_sched_list' ).show();
				
			} else if ( current_option == 'None' ) {
			
				jQuery( '#event_sched_list' ).hide();
				
				jQuery( '#recurrence-end' ).hide();
				
				jQuery( '#custom-recurrence-frequency' ).hide();	
				
			} else {
			
				jQuery( '#recurrence-end' ).show();
				
				jQuery( '#custom-recurrence-frequency' ).hide();
				
				jQuery( '#event_sched_list' ).show();
				
			}
		});
		
		jQuery( '[name="ignitewoo_event_info[recurrence][end-type]"]' ).change( function() {
		
			var val = jQuery( this ).find( 'option:selected' ).val();
			
			if ( val == 'On' ) {
			
				jQuery( '#rec-count' ).hide();
				
				jQuery( '#recurrence_end' ).show();
				
			} else {
			
				jQuery( '#recurrence_end' ).hide();
				
				jQuery( '#rec-count' ).show();
				
			}
		});
		
		jQuery( '[name="ignitewoo_event_info[recurrence][custom-type]"]' ).change( function() {
		
			jQuery( '.custom-recurrence-row' ).hide();
			
			var option = jQuery( this ).find( 'option:selected' ), custom_selector = option.data( 'tablerow' );
			
			jQuery( custom_selector ).show();
			
			jQuery( '#recurrence-interval-type' ).text(option.data( 'plural' )  );
			
			jQuery( '[name="ignitewoo_event_info[recurrence][custom-type-text]"]' ).val( option.data( 'plural' )  );
			
		});
		
		jQuery( '#recurrence_end_count' ).change( function() {
		
			jQuery( '[name="ignitewoo_event_info[recurrence][type]"]' ).change();
			
		});	
		
		jQuery( '[name="ignitewoo_event_info[recurrence][type]"]' ).change( function() {
		
			var option = jQuery( this ).find( 'option:selected' ), num_occurrences = jQuery( '#recurrence_end_count' ).val();
			
			jQuery( '#occurence-count-text' ).text( num_occurrences == 1 ? option.data( 'single' ) : option.data( 'plural' )  );
			
			jQuery( '[name="ignitewoo_event_info[recurrence][occurrence-count-text]"]' ).val(jQuery( '#occurence-count-text' ).text()  );
			
		});
		
		jQuery( '[name="ignitewoo_event_info[recurrence][custom-month-number]"]' ).change( function() {
		
			var option = jQuery( this ).find( 'option:selected' ), dayselect = jQuery( '[name="ignitewoo_event_info[recurrence][custom-month-day]"]' );
			
			if ( isNaN( option.val() ) ) { 
			
				dayselect.show();
				
			} else {
			
				dayselect.hide();
				
			}
			
		});
	});
	<?php } ?>
</script>