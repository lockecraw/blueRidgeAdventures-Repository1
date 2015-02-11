<?php
	global $woocommerce, $ignitewoo_events, $wpdb, $post; 
	
	$z = 0;
	
	$sl = new stdClass();
?>


		    <style>
			    #ignitewoo_events_wrap select { float: none; } 
			    #ignitewoo_events_wrap input { float: none; } 
			    #ignitewoo_events_wrap label { padding: 5px; } 

			    <?php if ( !empty( $_GET['message'] ) && '427' == $_GET['message'] ) { ?>

			    div.updated, .login .message {
				    background-color: #DEFFDE;
				    border-color: #55A505;
			    }

			    <?php } ?>

		    </style>

		    <p><label><?php _e( 'Sessions and Tracks', 'ignitewoo_events' )?></label></p>

		    <p class="description"><?php _e( 'The Session Title field is required for each session. If left empty then the entire session will be removed when updating the form!', 'ignitewoo_events' )?></p>

		    <p class="description"><?php _e( 'To rearrange the sessions, hover over the Move icon and drag the session to its new position.', 'ignitewoo_events' )?></p>

		    <div id="session_fields" class="panels" style="display:block !important">

			    <div class="event_session_fields">

				    <?php
				    $form_fields = get_post_meta( $post->ID, '_session_fields', true );

				    $loop = 0;

				    $orgs = new WP_Query( array( 'post_type' => 'event_organizer', 'post_status' => 'publish', 'posts_per_page' => 9999999, 'orderby' => 'title', 'order' => 'ASC'  ) ); 
				    $sponsors = new WP_Query( array( 'post_type' => 'event_sponsor', 'post_status' => 'publish', 'posts_per_page' => 9999999, 'orderby' => 'title', 'order' => 'ASC'  ) );
				    $speakers = new WP_Query( array( 'post_type' => 'event_speaker', 'post_status' => 'publish', 'posts_per_page' => 9999999, 'orderby' => 'title', 'order' => 'ASC'  ) );


				    if ( is_array( $form_fields ) && sizeof( $form_fields ) > 0 )
				    foreach ( $form_fields as $addon ) :
					    
					    if ( !$addon['name'] ) 
						continue;

					    if ( !isset($addon['required'] ) ) 
						$addon['required'] = 0;
					    
					    ?><div class="event_session_field">

						    <h4 class="first">
							    <?php _e( 'Event Session', 'ignitewoo_events' ) ?>
								<a class="ignitewoo_delete_session delete_addon" title="<?php _e( 'Delete This Item', 'ignitewoo_events' ); ?>" href="#">
								    <img style="cursor:pointer; margin:0 3px;float:right;" alt=" <?php _e( 'Delete this session', 'ignitewoo_events' )?> " title=" <?php _e( 'Delete this session', 'ignitewoo_events' )?> " src="<?php echo $ignitewoo_events->plugin_url ?>assets/images/delete.png">
								</a>
								<span class="handle"><?php _e( '&varr; Move', 'ignitewoo_events' ); ?></span>
								<span><a href="#" class="event_hide_handle" title="<?php _e(' Expand / Collapse', 'ignitewoo_events')?> " ><?php _e( '&varr; Hide / Show', 'ignitewoo_events' ); ?></a></span>
						    </h4>

						    <p class="form-field">
							    <label><?php _e( 'Session Title:', 'ignitewoo_events' ); ?></label>
							    <input type="text" class="event_form_addon_name event_form_active_name" name="ignitewoo_event_info[session_name][<?php echo $loop; ?>]" placeholder="<?php _e( 'Title', 'ignitewoo_events' ); ?>" value="<?php echo esc_attr($addon['name']); ?>" />  <img class="help_tip" data-tip="<?php _e( ' Enter a title for your session', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />
							    <input type="hidden" name="ignitewoo_event_info[session_position][<?php echo $loop; ?>]" class="addon_position" value="<?php echo $loop; ?>" />
						    </p>

						    <div class="event_session_inner"> 

							    <p class="form-field">
								    <label><?php _e( 'Date / Time:', 'ignitewoo_events' ); ?></label>
								    <input type="text" style="width:150px" class="event_form_addon_datetime datetimepicker" name="ignitewoo_event_info[session_date_time][<?php echo $loop; ?>]" placeholder="<?php _e( 'Date / Time', 'ignitewoo_events' ); ?>" value="<?php echo esc_attr($addon['datetime']); ?>" />  <img class="help_tip" data-tip="<?php _e( ' Optionally select the date / time for this session', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />
							    </p>

							    <p class="form-field">
								    <label><?php _e( 'Session Organizers', 'ignitewoo_events' ); ?></label>
								    <?php if ( !isset( $orgs->posts ) || count( $orgs->posts ) <= 0 ) { ?>
									    <?php _e( 'No organizers exist. Before you can add organizers you must create and publish one', 'ignitewoo_events' ) ?>
								    <?php } else { ?>
									    <select class="multiselect chosen_select chosen" multiple="multiple" style="width: 250px" id="ignitewoo_event_organizer_select_<?php echo $loop; ?>" name="ignitewoo_event_info[session_organizer][<?php echo $loop; ?>]">
										    <?php foreach ( $orgs->posts as $p ) { ?>
											    <option <?php if ( in_array( $p->ID, (array)$addon['organizer'] ) ) echo 'selected="selected"' ?> value="<?php echo $p->ID ?>"><?php echo get_the_title( $p->ID ) ?></option>
										    <?php } ?>
									    </select>
									<img class="help_tip" data-tip="<?php _e( ' Optionally select organizers specific to this session ', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />
								    <?php } ?>
							    </p>

							    <p class="form-field">
								    <label><?php _e( 'Session Sponsors', 'ignitewoo_events' ); ?></label>
								    <?php if ( !isset( $sponsors->posts ) || count( $sponsors->posts ) <= 0 ) { ?>
									    <?php _e( 'No sponsors exist. Before you can add sponsors you must create and publish one', 'ignitewoo_events' ) ?>
								    <?php } else { ?>
									    <select class="multiselect chosen_select chosen" multiple="multiple" style="width: 250px" id="ignitewoo_event_sponsor_select_<?php echo $loop; ?>" name="ignitewoo_event_info[session_sponsor][<?php echo $loop; ?>]">
										    <?php foreach ( $sponsors->posts as $p ) { ?>
											    <option <?php if ( in_array( $p->ID, (array)$addon['sponsor'] ) ) echo 'selected="selected"' ?> value="<?php echo $p->ID ?>"><?php echo get_the_title( $p->ID ) ?></option>
										    <?php } ?>
									    </select>
									    <img class="help_tip" data-tip="<?php _e( ' Optionally select sponsors specific to this session ', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />
								    <?php } ?>
							    </p>

							    <p class="form-field">
								    <label><?php _e( 'Description:', 'ignitewoo_events' ); ?>  <img class="help_tip" data-tip="<?php _e( ' Optionally enter a description specific to this session ', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" /></label>
								    <textarea style="width:515px;height:75px" name="ignitewoo_event_info[session_description][<?php echo $loop; ?>]" placeholder="<?php _e( 'Description', 'ignitewoo_events' ); ?>"><?php echo esc_attr($addon['description']); ?></textarea>
							    </p>

							    <div class="event_form_speaker_wrap">

								    <?php
								    $sql = 'select post_title, post_content, m1.meta_value as speaker_id, m2.meta_value as speaker_track, m3.meta_value as speaker_position, m4.meta_value as speaker_start, m5.meta_value as speaker_end  
									from ' . $wpdb->posts . ' 
									left join ' . $wpdb->postmeta . ' m1 on ID = m1.post_id 
									left join ' . $wpdb->postmeta . ' m2 on ID = m2.post_id 
									left join ' . $wpdb->postmeta . ' m3 on ID = m3.post_id 
									left join ' . $wpdb->postmeta . ' m4 on ID = m4.post_id 
									left join ' . $wpdb->postmeta . ' m5 on ID = m5.post_id 
									where post_parent = "' . $post->ID . '" and post_type = "event_track_speaker"
									and m1.meta_key = "speaker_id" 
									and m2.meta_key = "speaker_track" 
									and m3.meta_key = "speaker_position" 
									and m4.meta_key = "speaker_start"
									and m5.meta_key = "speaker_end"
									order by m3.meta_value ASC
								    ';

								    $speaker_list = $wpdb->get_results( $sql );

								    $z = 0;
								    
								    if ( $speaker_list )
								    foreach( $speaker_list as $sl ) { 

									   if ( $sl->speaker_track != $loop ) 
										continue;
								    ?>
									<div class="single_speaker_wrap">
										<div class="speaker">
											    <p class="form-field">
												    <?php _e( "Speaker", "woocommerce" ) ?>: <strong><?php echo $sl->post_title ?></strong>
												    <a class="ignitewoo_delete_session delete_speaker" title="<?php _e( 'Delete This Item', 'ignitewoo_events' ); ?>" href="#">
													<img style="cursor:pointer; margin:0 3px;float:right;" alt=" <?php _e( 'Delete this speaker', 'ignitewoo_events' )?> " title=" <?php _e( 'Delete this speaker', 'ignitewoo_events' )?> " src="<?php echo $ignitewoo_events->plugin_url ?>assets/images/delete.png">
												    </a>
												    <span class="speaker_handle"><?php _e( '&varr; Move', 'ignitewoo_events' ); ?></span>
												    <span><a href="#" class="event_speaker_handle" title="<?php _e(' Expand / Collapse', 'ignitewoo_events')?> " ><?php _e( '&varr; Hide / Show', 'ignitewoo_events' ); ?></a></span>
											    </p>
											    <div class="speaker_inner" style="display:none">
												    <p class="form-field">
													    <label><?php _e( 'Times', 'ignitewoo_events' )?></label>
													    <?php _e( 'Start', 'ignitewoo_events' ) ?> &nbsp; <input type="text" style="width:80px" class="timepicker" name="ignitewoo_event_info[session_speaker_start][<?php echo $loop ?>][<?php echo $z ?>]" value="<?php echo $sl->speaker_start ?>" > &nbsp; 
													    <?php _e( 'End', 'ignitewoo_events' ) ?> &nbsp; <input type="text" style="width:80px" class="timepicker" name="ignitewoo_event_info[session_speaker_end][<?php echo $loop ?>][<?php echo $z ?>]" value="<?php echo $sl->speaker_end ?>" >
												    </p>
												    <p class="form-field">
													    <label><?php _e( 'Track Description', 'ignitewoo_events' )?></label>
													    <input type="hidden" name="ignitewoo_event_info[session_speaker_id][<?php echo $loop ?>][<?php echo $z ?>]" value="<?php echo $sl->speaker_id ?>" >
													    <textarea class="speaker_desc" style="width:99%;height:75px" name="ignitewoo_event_info[session_speaker_desc][<?php echo $loop ?>][<?php echo $z ?>]"><?php echo $sl->post_content ?></textarea>
												    </p>
											    </div>
										</div>
									</div>
									<?php $z++ ?>
								    <?php }  ?>

							    </div>

							    <p class="form-field">
								    <label><?php _e( 'Session Speakers', 'ignitewoo_events' ); ?> <img class="help_tip" data-tip="<?php _e( ' Optionally select speakers specific to this session ', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" /></label>

								    <?php if ( !isset( $speakers->posts ) || count( $speakers->posts ) <= 0 ) { ?>
									    <?php _e( 'No speakers exist. Before you can add speakers you must create and publish one', 'ignitewoo_events' ) ?>
								    <?php } else { ?>
									    <select class="chosen_select chosen" style="width: 250px" id="ignitewoo_event_speaker_select_<?php echo $loop; ?>" name="ignitewoo_event_info_session_speaker_select">
										    <option value=""><?php _e( 'Select speaker', 'ignitewoo_events' )?></option>
										    <?php foreach ( $speakers->posts as $p ) { ?>
										    <option value="<?php echo $p->ID ?>"><?php echo get_the_title( $p->ID ) ?></option>
										    <?php } ?>
									    </select>
									    &nbsp; <input style="margin-left: 8px; position:relative; top:-9px" type="button" name="event_form_speaker_new" class="button event_form_speaker_add" value=" <?php _e( 'Add', 'ignitewoo_events' )?> ">
									    <?php // <?php if ( in_array( $p->ID, (array)$addon['speaker'] ) ) echo 'selected="selected"' ?>  

								    <?php } ?>
							    </p>

						    </div>

					    </div><?php
					    
					    $loop++;
						
				    endforeach;
				    ?>
				    
			    </div>
			    
			    <h4>
				    <?php /*<a href="#" class="import tips" tip="<?php _e( 'Import', 'ignitewoo_events' ); ?>"><?php _e( 'Import', 'ignitewoo_events' ); ?></a>
				    <a href="#" class="export tips" tip="<?php _e( 'Export', 'ignitewoo_events' ); ?>"><?php _e( 'Export', 'ignitewoo_events' ); ?></a>
				    */ ?>
				    <a href="#" class="ignitewoo_event_add_new_addon button"><?php _e( '+ Add New Session', 'ignitewoo_events' ); ?></a>
			    </h4>
			    <?php /*
			    <textarea name="export_product_addon" class="export" cols="20" rows="5" readonly="readonly"><?php echo esc_textarea( serialize($form_fields) ); ?></textarea>
			    <textarea name="import_product_addon" class="import" cols="20" rows="5" placeholder="<?php _e( 'Paste form data to import here then save the product. The imported fields will be appended.', 'ignitewoo_events' ); ?>"></textarea>
			    */ ?>
		    </div>

			<script type="text/javascript">
			<?php /*
			jQuery( document ).ready( function() { 
				jQuery( ".event_form_type_select" ).change( function() { 
					if ( ( "checkbox" == jQuery( this ).val() ) || ( "radiobutton" == jQuery( this ).val() ) || ( "select" == jQuery( this ).val() ) )
						jQuery( this ).parent().parent().find( ".event_session_field_options" ).removeClass( "hidden" );
					else
						jQuery( this ).parent().parent().find( ".event_session_field_options" ).addClass( "hidden" );
				});
			});
			*/ ?>

			jQuery( document ).ready( function() { 

				jQuery( ".datetimepicker" ).datetimepicker();

				jQuery( ".timepicker" ).datetimepicker( { timeOnly: true, ampm: true } );

				jQuery( "#post" ).submit( function() { 

					var c = jQuery( "input[name='_ignitewoo_event']" ).attr( "checked" );

					if ( null == c || "checked" != c )
						return true;

					missing_session_titles = false;
					missing_item = null;

					jQuery( ".event_form_active_name" ).each( function() { 
						if ( !jQuery( this ).closest( '.event_session_field' ).is( 'visible' ) )
							return;
						if ( null == jQuery( this ).val() || 0 == jQuery( this ).val().length )
							missing_session_titles = true;
							missing_item = jQuery( this );

					});

					if ( missing_session_titles ) {
						alert( "<?php _e( 'The TITLE fields for sessions cannot be blank.\nEnter a title or remove the item.', 'ignitewoo_events' ) ?>" );
						jQuery( "#ajax-loading" ).hide();
						jQuery( "#publish" ).removeClass( "button-primary-disabled" );
						/*
						jQuery( "html, body" ).animate({
							scrollTop: ( missing_item.offset().top - 100 )
							}, 1000);
						*/
						return false;
					}

					var d = jQuery( "input[name='ignitewoo_event_info[start_date]']" ).val();
					var e = jQuery( "input[name='ignitewoo_event_info[end_date]']" ).val();

					if ( null == d || 0 == d.length || null == e|| 0 == e.length ) { 

						alert( "<?php _e( 'You must enter a start date and end date.', 'ignitewoo_events' ) ?>" );
								jQuery( "#ajax-loading" ).hide();
								jQuery( "#publish" ).removeClass( "button-primary-disabled" );
								/*jQuery( "html, body" ).animate({
									scrollTop: ( d.offset().top - 100 )
									}, 1000);
								*/
						return false;
					}

					return true;


				});

				jQuery( ".event_hide_handle" ).click( function() { 
					jQuery( this ).parent().parent().parent().find( ".event_session_inner" ).toggle( "fast" );
					return false;
				});

				jQuery( ".event_speaker_handle" ).click( function() { 
					jQuery( this ).parent().parent().parent().find( ".speaker_inner" ).toggle( "fast" );
					return false;
				});


				jQuery( ".event_form_speaker_add" ).live( "click", function() { 

					speaker = jQuery( this ).parent().find( "select")

					speaker_id = speaker.val();

					speaker_name = speaker.find( ":selected" ).html();

					position = jQuery( this ).parent().parent().parent().find( ".addon_position" ).val();

					loop = jQuery( this ).closest( ".event_session_inner").find(".single_speaker_wrap" ).length;

					if ( null == loop || 0 == loop.length )
						loop = 0;
					
					if ( null == speaker_id || 0 == speaker_id.length )
						return;

					jQuery( this ).parent().parent().find( ".event_form_speaker_wrap" ).append( '<div class="single_speaker_wrap">\
						<div class="speaker">\
						    <p class="form-field"><?php _e( "Speaker", "woocommerce" ) ?>:\
						    <strong>' + speaker_name + '</strong>\
							    <a class="ignitewoo_delete_session delete_speaker" title="<?php _e( 'Delete This Item', 'ignitewoo_events' ); ?>" href="#">\
								<img style="cursor:pointer; margin:0 3px;float:right;" alt=" <?php _e( 'Delete this speaker', 'ignitewoo_events' )?> " title=" <?php _e( 'Delete this speaker', 'ignitewoo_events' )?> " src="<?php echo $ignitewoo_events->plugin_url ?>assets/images/delete.png">\
							    </a>\
							    <span class="speaker_handle"><?php _e( '&varr; Move', 'ignitewoo_events' ); ?></span>\
						    </p>\
						    <div class="speaker_inner">\
						    <p class="form-field">\
							    <label><?php _e( 'Times', 'ignitewoo_events' )?></label>\
							    <?php _e( 'Start', 'ignitewoo_events' ) ?> &nbsp; <input type="text" style="width:80px" class="timepicker" name="ignitewoo_event_info[session_speaker_start][' + position + '][<?php echo $z ?>]" value="<?php echo isset( $sl->speaker_start ) ? $sl->speaker_start : ''?>" > &nbsp; \
							    <?php _e( 'End', 'ignitewoo_events' ) ?> &nbsp; <input type="text" style="width:80px" class="timepicker" name="ignitewoo_event_info[session_speaker_end][' + position + '][<?php echo $z ?>]" value="<?php echo isset( $sl->speaker_start ) ? $sl->speaker_end : '' ?>" >\
						    </p>\
						    <p class="form-field">\
						    <label><?php _e( 'Track Description', 'ignitewoo_events' )?></label>\
						    <input type="hidden" name="ignitewoo_event_info[session_speaker_id][' + position + '][' + loop + ']" value="' + speaker_id + '" >\
						    <textarea class="speaker_desc" style="width:99%;height:75px" name="ignitewoo_event_info[session_speaker_desc][' + position + '][' + loop + ']"></textarea>\
						    </p>\
						    <div>\
						</div>\
					</div>');

					jQuery( ".timepicker" ).datetimepicker( { timeOnly: true, ampm: true } );

				});

				jQuery( "a.ignitewoo_event_add_new_addon" ).live( "click", function(){

					var loop = jQuery( ".event_session_fields .event_session_field" ).length;

					jQuery( ".event_session_fields" ).append( '<div class="event_session_field">\
						    <h4 class="first">\
							    <?php _e( 'Event Session', 'ignitewoo_events' ) ?>\
								<a class="ignitewoo_delete_session delete_addon" title="<?php _e( 'Delete This Item', 'ignitewoo_events' ); ?>" href="#">\
								    <img style="cursor:pointer; margin:0 3px;float:right;" alt=" <?php _e( 'Delete this session', 'ignitewoo_events' )?> " title=" <?php _e( 'Delete this session', 'ignitewoo_events' )?> " src="<?php echo $ignitewoo_events->plugin_url ?>assets/images/delete.png">\
								</a>\
								<span class="handle"><?php _e( '&varr; Move', 'ignitewoo_events' ); ?></span>\
								<span><a href="#" class="event_hide_handle" title="<?php _e(' Expand / Collapse', 'ignitewoo_events')?> " ><?php _e( '&varr; Hide / Show', 'ignitewoo_events' ); ?></a></span>\
						    </h4>\
						    <p class="form-field">\
							    <label><?php _e( 'Title:', 'ignitewoo_events' ); ?></label>\
							    <input type="text" class="event_form_addon_name" name="ignitewoo_event_info[session_name][' + loop + ']" placeholder="<?php _e( 'Session Title', 'ignitewoo_events' ); ?>" value="" />  <img class="help_tip" data-tip="<?php _e( ' Enter a title for your session ', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />\
							    <input type="hidden" name="ignitewoo_event_info[session_position][' + loop + ']" class="addon_position" value="' + loop + '" />\
						    </p>\
						    <div class="event_session_inner" style="display:block"> \
							<p class="form-field">\
								<label><?php _e( 'Date / Time:', 'ignitewoo_events' ); ?></label>\
								<input type="text" style="width:150px" class="event_form_addon_datetime datetimepicker" name="ignitewoo_event_info[session_date_time][' + loop + ']" placeholder="<?php _e( 'Date / Time', 'ignitewoo_events' ); ?>" value="" />  <img class="help_tip" data-tip="<?php _e( ' Optionally select the date / time for this session / track ', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />\
							</p>\
							<p class="form-field">\
								<label><?php _e( 'Session Organizers', 'ignitewoo_events' ); ?></label>\
								<?php if ( !isset( $orgs->posts ) || count( $orgs->posts ) <= 0 ) { ?>
									<?php _e( 'No organizers exist. Before you can add organizers you must create and publish one\\', 'ignitewoo_events' ) ?>
								<?php } else { ?>
									<select class="multiselect chosen_select chosen" multiple="multiple" style="width: 250px" id="ignitewoo_event_organizer_select_'  + loop + '" name="ignitewoo_event_info[session_organizer][' + loop + ']">\
										<?php foreach ( $orgs->posts as $p ) { ?>
											<option value="<?php echo $p->ID ?>"><?php echo get_the_title( $p->ID ) ?></option>\
										<?php } ?>
									</select>\
								    <img class="help_tip" data-tip="<?php _e( ' Optionally select organizers specific to this session / track ', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />\
								<?php } ?>
							</p>\
							<p class="form-field">\
								<label><?php _e( 'Session Sponsors', 'ignitewoo_events' ); ?>  <img class="help_tip" data-tip="<?php _e( ' Optionally select sponsors specific to this session / track ', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" /></label>\
								<?php if ( !isset( $sponsors->posts ) || count( $sponsors->posts ) <= 0 ) { ?>
									<?php _e( 'No sponsors exist. Before you can add sponsors you must create and publish one\\', 'ignitewoo_events' ) ?>
								<?php } else { ?>
									<select class="multiselect chosen_select chosen" multiple="multiple" style="width: 250px" id="ignitewoo_event_sponsor_select_'  + loop + '" name="ignitewoo_event_info[session_sponsor][' + loop + ']">\
										<?php foreach ( $sponsors->posts as $p ) { ?>
											<option value="<?php echo $p->ID ?>"><?php echo get_the_title( $p->ID ) ?></option>\
										<?php } ?>
									</select>\
									<img class="help_tip" data-tip="<?php _e( ' Optionally select sponsors specific to this session / track ', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" />\
								<?php } ?>
							</p>\
							<p class="form-field">\
								<label><?php _e( 'Description:', 'ignitewoo_events' ); ?>  <img class="help_tip" data-tip="<?php _e( ' Optionally enter a description specific to this session ', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" /></label>\
								<textarea style="width:515px;height:75px" name="ignitewoo_event_info[session_description][<?php echo $loop; ?>]" placeholder="<?php _e( 'Description', 'ignitewoo_events' ); ?>"></textarea>\
							</p>\
							<div class="event_form_speaker_wrap">\
							</div>\
							<p class="form-field">\
								<label><?php _e( 'Session Speakers', 'ignitewoo_events' ); ?> <img class="help_tip" data-tip="<?php _e( ' Optionally select speakers specific to this session / track ', 'ignitewoo_events' ) ?>" src="<?php echo $woocommerce->plugin_url() ?>/assets/images/help.png" /></label>\
								<?php if ( !isset( $speakers->posts ) || count( $speakers->posts ) <= 0 ) { ?>
									<?php _e( 'No speakers exist. Before you can add speakers you must create and publish one\\', 'ignitewoo_events' ) ?>
								<?php } else { ?>
									<select class="chosen_select chosen"style="width: 250px" id="ignitewoo_event_speaker_select_'  + loop + '" name="ignitewoo_event_info[session_speaker][' + loop + ']">\
										<option value=""><?php _e( 'Select speaker', 'ignitewoo_events' )?></option>\
										<?php foreach ( $speakers->posts as $p ) { ?>
											<option value="<?php echo $p->ID ?>"><?php echo get_the_title( $p->ID ) ?></option>\
										<?php } ?>
									</select>\
									    &nbsp; <input style="margin-left: 8px; position:relative; top:-9px" type="button" name="event_form_speaker_new" class="button event_form_speaker_add" value=" <?php _e( 'Add', 'ignitewoo_events' )?> ">\
								<?php } ?>
							</p>\
						    </div>\
					</div>');

					<?php /*
					jQuery( ".event_form_type_select" ).change( function() { 
						if ( ( "checkbox" == jQuery( this ).val() ) || ( "radiobutton" == jQuery( this ).val() ) || ( "select" == jQuery( this ).val() ) )
							jQuery( this ).parent().parent().find( ".event_session_field_options" ).removeClass( "hidden" );
						else
							jQuery( this ).parent().parent().find( ".event_session_field_options" ).addClass( "hidden" );
					});
					*/ ?>

					jQuery( ".event_form_addon_name" ).addClass( "event_form_active_name" );

					jQuery( ".chosen" ).chosen();

					jQuery(".tips, .help_tip").tipTip({
							'attribute' : 'data-tip',
							'fadeIn' : 50,
							'fadeOut' : 50,
							'delay' : 200
					});

					jQuery( ".datetimepicker" ).datetimepicker();

					//jQuery( ".event_hide_handle" ).click( function() { 
					//	jQuery( this ).parent().parent().parent().find( ".event_session_inner" ).toggle( "fast" );
					//	return false;
					//});

					return false;
					
				});
				
				jQuery('button.add_addon_option').live('click', function(){
					
					var loop = jQuery(this).closest('.event_session_field').index('.event_session_field');
					
					jQuery(this).closest('.event_session_field_options').find('tbody').append('<tr>\
						<td><input type="text" name="ignitewoo_event_info[session_option_label][' + loop + '][]" placeholder="<?php _e( 'Label', 'ignitewoo_events' ); ?>" /></td>\
						<td><input type="text" name="ignitewoo_event_info[session_option_price][' + loop + '][]" placeholder="0.00" /></td>\
						<td class="actions"><button type="button" class="remove_addon_option button">x</button></td>\
					</tr>');
					
					return false;
		
				});
				
				jQuery('button.remove_addon_option').live('click', function(){
				
					var answer = confirm('<?php _e( 'Are you sure you want delete this form option?', 'ignitewoo_events' ); ?>');
		
					if (answer) {
						jQuery(this).closest('tr').remove();
					}
					
					return false;
		
				});

				jQuery('a.delete_speaker').live('click', function(){
				
					var answer = confirm('<?php _e( 'Are you sure you want delete this speaker from this session?', 'ignitewoo_events' ); ?>');
		
					if ( answer ) {
						var addon = jQuery(this).closest( ".speaker" );
						addon.remove();
						//jQuery( addon ).find( "input" ).val('');
						//jQuery( addon ).hide();
					}
					
					return false;
		
				});

				jQuery('a.delete_addon').live('click', function(){
				
					var answer = confirm('<?php _e( 'Are you sure you want delete this?', 'ignitewoo_events' ); ?>');
		
					if (answer) {
						var addon = jQuery(this).closest('.event_session_field');
						jQuery(addon).find('input').val('');
						jQuery(addon).hide();
					}
					
					return false;
		
				});
				
				jQuery('.event_session_field table.event_session_field_options tbody').sortable({
					items:'tr',
					cursor:'move',
					axis:'y',
					scrollSensitivity:40,
					helper:function(e,ui){
						ui.children().each(function(){
							jQuery(this).width(jQuery(this).width());
						});
						return ui;
					},
					start:function(event,ui){
						ui.item.css('background-color','#f6f6f6');
					},
					stop:function(event,ui){
						ui.item.removeAttr('style');
					}
				});
				
				jQuery('.event_session_fields').sortable({
					items:'.event_session_field',
					cursor:'move',
					axis:'y',
					handle:'.handle',
					scrollSensitivity:40,
					helper:function(e,ui){
						ui.children().each(function(){
							jQuery(this).width(jQuery(this).width());
						});
						return ui;
					},
					start:function(event,ui){
						ui.item.css('border-style','dashed');
					},
					stop:function(event,ui){
						ui.item.removeAttr('style');
						addon_row_indexes();
					}
				});


				jQuery('.event_form_speaker_wrap').sortable({
					items:'.single_speaker_wrap',
					cursor:'move',
					axis:'y',
					handle:'.speaker_handle',
					scrollSensitivity:40,
					helper:function(e,ui){
						ui.children().each(function(){
							jQuery(this).width(jQuery(this).width());
						});
						return ui;
					},
					start:function(event,ui){
						ui.item.css('border-style','dashed');
						ui.item.css('border-color','#cccccc');
					},
					stop:function(event,ui){
						ui.item.removeAttr('style');
						addon_row_indexes();
					}
				});

				function addon_row_indexes() {
					jQuery('.event_session_fields .event_session_field').each(function(index, el){ jQuery('.addon_position', el).val( parseInt( jQuery(el).index('.event_session_fields .event_session_field') ) ); });
				};
				
				jQuery('#session_fields').on('click', 'a.export', function() {
					
					jQuery('#session_fields textarea.import').hide();
					jQuery('#session_fields textarea.export').slideToggle('500', function() {
						jQuery(this).select();
					});
					
					return false;
				});
				
				jQuery('#session_fields').on('click', 'a.import', function() {
					
					jQuery('#session_fields textarea.export').hide();
					jQuery('#session_fields textarea.import').slideToggle('500', function() {
						jQuery(this).val('');
					});
					
					return false;
				});
				
			});
			</script>

	<?php unset( $speakers ) ?>
	<?php unset( $sponsors ) ?>
	<?php unset( $orgs ) ?>
