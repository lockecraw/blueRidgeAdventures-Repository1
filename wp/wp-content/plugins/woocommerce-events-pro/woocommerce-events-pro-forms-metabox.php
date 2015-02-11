<?php
	function meta_box( $post ) {
		    ?>

		    <p class="description" style="margin-top: 10px"><?php _e( 'All visible input fields except price are required for each form field. Otherwise the form field will be removed when updating the form.', 'ignitewoo_events' )?></p>

		    <p class="description"><?php _e( 'To rearrange the fields, hover over the Move icon and drag the field to its new position.', 'ignitewoo_events' )?></p>

		    <div id="form_fields" class="panel">

			    <div class="event_form_fields">

				    <?php
				    $form_fields = get_post_meta( $post->ID, '_form_fields', true );

				    $loop = 0;
				    
				    if ( is_array( $form_fields ) && sizeof( $form_fields ) > 0 ) 
				    foreach ( $form_fields as $addon ) :
					    
					    if ( !$addon['name'] ) 
						continue;

					    if ( !isset($addon['required'] ) ) 
						$addon['required'] = 0;
					    
					    ?><div class="event_form_field">
						    <p class="addon_name">
							    <label class="hidden"><?php _e( 'Name:', 'ignitewoo_events' ); ?></label>
							    <input type="text" name="addon_name[<?php echo $loop; ?>]" placeholder="<?php _e( 'Name', 'ignitewoo_events' ); ?>" value="<?php echo esc_attr($addon['name']); ?>" />
							    <input type="hidden" name="addon_position[<?php echo $loop; ?>]" class="addon_position" value="<?php echo $loop; ?>" />
						    </p>
						    <p class="addon_type">
							    <label class="hidden"><?php _e( 'Type:', 'ignitewoo_events' ); ?></label>
							    <select class="event_form_type_select" name="addon_type[<?php echo $loop; ?>]">
								    <option <?php selected( 'custom', $addon['type'] ); ?> value="custom"><?php _e( 'Single line text input', 'ignitewoo_events' ); ?></option>
								    <option <?php selected( 'custom_textarea', $addon['type'] ); ?> value="custom_textarea"><?php _e( 'Multi-line text input', 'ignitewoo_events' ); ?></option>
								    <option <?php selected( 'checkbox', $addon['type'] ); ?> value="checkbox"><?php _e( 'Checkboxes', 'ignitewoo_events' ); ?></option>
								    <option <?php selected( 'radiobutton', $addon['type'] ); ?> value="radiobutton"><?php _e( 'Radio buttons', 'ignitewoo_events' ); ?></option>
								    <option <?php selected( 'select', $addon['type'] ); ?> value="select"><?php _e( 'Select box', 'ignitewoo_events' ); ?></option>
								    <option <?php selected('file_upload', $addon['type']); ?> value="file_upload"><?php _e( 'File upload', 'ignitewoo_events' ); ?></option>
							    </select>
						    </p>
						    <p class="addon_description">
							    <label class="hidden"><?php _e( 'Description:', 'ignitewoo_events' ); ?></label>
							    <input type="text" name="addon_description[<?php echo $loop; ?>]" placeholder="<?php _e( 'Description', 'ignitewoo_events' ); ?>" value="<?php echo esc_attr($addon['description']); ?>" />
						    </p>
						    <p class="addon_required">
							    <label><input type="checkbox" name="addon_required[<?php echo $loop; ?>]" <?php checked($addon['required'], 1) ?> /> <?php _e( 'Required field', 'ignitewoo_events' ); ?></label>
						    </p>
						    <?php /*

							    if ( 'checkbox' == $addon['type'] || 'radiobutton' == $addon['type'] || 'select' == $addon['type'] )
								    $class = ''; 
							    else 
								    $class = 'hidden';

						    */ ?>
						    <table cellpadding="0" cellspacing="0" class="event_form_field_options <?php echo $class ?>" rel="<?php echo $loop; ?>">
							    <thead>
								    <tr>
									    <th><?php _e( 'Options or Labels', 'ignitewoo_events' ); ?></th>
									    <th><?php _e( 'Price:', 'ignitewoo_events' ); ?></th>
									    <th width="1%" class="actions"><button type="button" class="add_addon_option button"><?php _e( 'Add', 'ignitewoo_events' ); ?></button></th>
								    </tr>
							    </thead>
							    <tbody>	
								    <?php
								    foreach ($addon['options'] as $option) :
									    ?>
									    <tr>
										    <td><input type="text" name="addon_option_label[<?php echo $loop; ?>][]" value="<?php echo esc_attr($option['label']) ?>" placeholder="<?php _e( 'Label', 'ignitewoo_events' ); ?>" /></td>
										    <td><input type="text" name="addon_option_price[<?php echo $loop; ?>][]" value="<?php echo esc_attr($option['price']) ?>" placeholder="0.00" /></td>
										    <td class="actions"><button type="button" class="remove_addon_option button">x</button></td>
									    </tr>
									    <?php
								    endforeach;
								    ?>	
							    </tbody>
						    </table>
						    <span class="handle"><?php _e( '&varr; Move', 'ignitewoo_events' ); ?></span>
						    <a href="#" class="delete_addon"><?php _e( 'Delete field', 'ignitewoo_events' ); ?></a>
					    </div><?php
					    
					    $loop++;
						
				    endforeach;
				    ?>
				    
			    </div>
			    
			    <h4>
				    <?php /*<a href="#" class="import tips" tip="<?php _e( 'Import', 'ignitewoo_events' ); ?>"><?php _e( 'Import', 'ignitewoo_events' ); ?></a>
				    <a href="#" class="export tips" tip="<?php _e( 'Export', 'ignitewoo_events' ); ?>"><?php _e( 'Export', 'ignitewoo_events' ); ?></a>
				    */ ?>
				    <a href="#" class="add_new_addon button"><?php _e( '+ Add New Field', 'ignitewoo_events' ); ?></a>
			    </h4>
			    
			    <textarea name="export_product_addon" class="export" cols="20" rows="5" readonly="readonly"><?php echo esc_textarea( serialize($form_fields) ); ?></textarea>
			    <textarea name="import_product_addon" class="import" cols="20" rows="5" placeholder="<?php _e( 'Paste form data to import here then save the product. The imported fields will be appended.', 'ignitewoo_events' ); ?>"></textarea>
		    </div>
			<script type="text/javascript">
			<?php /*
			jQuery( document ).ready( function() { 
				jQuery( ".event_form_type_select" ).change( function() { 
					if ( ( "checkbox" == jQuery( this ).val() ) || ( "radiobutton" == jQuery( this ).val() ) || ( "select" == jQuery( this ).val() ) )
						jQuery( this ).parent().parent().find( ".event_form_field_options" ).removeClass( "hidden" );
					else
						jQuery( this ).parent().parent().find( ".event_form_field_options" ).addClass( "hidden" );
				});
			});
			*/ ?>
			jQuery(function(){

				jQuery('#form_fields').on('click', 'a.add_new_addon', function(){

					var loop = jQuery('.event_form_fields .event_form_field').size();
					
					jQuery('.event_form_fields').append('<div class="event_form_field">\
						<p class="addon_name">\
							<label class="hidden"><?php _e( 'Name:', 'ignitewoo_events' ); ?></label>\
							<input type="text" name="addon_name[' + loop + ']" placeholder="<?php _e( 'Name', 'ignitewoo_events' ); ?>" />\
							<input type="hidden" name="addon_position[' + loop + ']" class="addon_position" value="' + loop + '" />\
						</p>\
						<p class="addon_type">\
							<label class="hidden"><?php _e( 'Type:', 'ignitewoo_events' ); ?></label>\
							<select class="event_form_type_select" name="addon_type[' + loop + ']">\
								<option value="custom"><?php _e( 'Single line text input', 'ignitewoo_events' ); ?></option>\
								<option value="custom_textarea"><?php _e( 'Multi-line text input', 'ignitewoo_events' ); ?></option>\
								<option value="checkbox"><?php _e( 'Checkboxes', 'ignitewoo_events' ); ?></option>\
								<option value="radiobutton"><?php _e( 'Radio buttons', 'ignitewoo_events' ); ?></option>\
								<option value="select"><?php _e( 'Select box', 'ignitewoo_events' ); ?></option>\
								<option value="file_upload"><?php _e( 'File upload', 'ignitewoo_events' ); ?></option>\
							</select>\
						</p>\
						<p class="addon_description">\
							<label class="hidden"><?php _e( 'Description:', 'ignitewoo_events' ); ?></label>\
							<input type="text" name="addon_description[' + loop + ']" placeholder="<?php _e( 'Description', 'ignitewoo_events' ); ?>" />\
						</p>\
						<p class="addon_required">\
							<label><input type="checkbox" name="addon_required[' + loop + ']" /> <?php _e( 'Required field', 'ignitewoo_events' ); ?></label>\
						</p>\
						<table cellpadding="0" cellspacing="0" class="event_form_field_options" rel="' + loop +'">\
							<thead>\
								<tr>\
									<th><?php _e( 'Option Labels', 'ignitewoo_events' ); ?></th>\
									<th><?php _e( 'Price:', 'ignitewoo_events' ); ?></th>\
									<th width="1%" class="actions"><button type="button" class="add_addon_option button"><?php _e( 'Add', 'ignitewoo_events' ); ?></button></th>\
								</tr>\
							</thead>\
							<tbody>\
								<tr>\
									<td><input type="text" name="addon_option_label[' + loop + '][]" value="<?php ?>" placeholder="<?php _e( 'Label', 'ignitewoo_events' ); ?>" /></td>\
									<td><input type="text" name="addon_option_price[' + loop + '][]" value="<?php ?>" placeholder="0.00" /></td>\
									<td class="actions"><button type="button" class="remove_addon_option button">x</button></td>\
								</tr>\
							</tbody>\
						</table>\
						<span class="handle"><?php _e( '&varr; Move', 'ignitewoo_events' ); ?></span>\
						<a href="#" class="delete_addon"><?php _e( 'Delete field', 'ignitewoo_events' ); ?></a>\
					</div>');
					<?php /*
					jQuery( ".event_form_type_select" ).change( function() { 
						if ( ( "checkbox" == jQuery( this ).val() ) || ( "radiobutton" == jQuery( this ).val() ) || ( "select" == jQuery( this ).val() ) )
							jQuery( this ).parent().parent().find( ".event_form_field_options" ).removeClass( "hidden" );
						else
							jQuery( this ).parent().parent().find( ".event_form_field_options" ).addClass( "hidden" );
					});
					*/ ?>
					return false;
					
				});
				
				jQuery('button.add_addon_option').live('click', function(){
					
					var loop = jQuery(this).closest('.event_form_field').index('.event_form_field');
					
					jQuery(this).closest('.event_form_field_options').find('tbody').append('<tr>\
						<td><input type="text" name="addon_option_label[' + loop + '][]" placeholder="<?php _e( 'Label', 'ignitewoo_events' ); ?>" /></td>\
						<td><input type="text" name="addon_option_price[' + loop + '][]" placeholder="0.00" /></td>\
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
				
				jQuery('a.delete_addon').live('click', function(){
				
					var answer = confirm('<?php _e( 'Are you sure you want delete this field?', 'ignitewoo_events' ); ?>');
		
					if (answer) {
						var addon = jQuery(this).closest('.event_form_field');
						jQuery(addon).find('input').val('');
						jQuery(addon).hide();
					}
					
					return false;
		
				});
				
				jQuery('.event_form_field table.event_form_field_options tbody').sortable({
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
				
				jQuery('.event_form_fields').sortable({
					items:'.event_form_field',
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
				
				function addon_row_indexes() {
					jQuery('.event_form_fields .event_form_field').each(function(index, el){ jQuery('.addon_position', el).val( parseInt( jQuery(el).index('.event_form_fields .event_form_field') ) ); });
				};
				
				jQuery('#form_fields').on('click', 'a.export', function() {
					
					jQuery('#form_fields textarea.import').hide();
					jQuery('#form_fields textarea.export').slideToggle('500', function() {
						jQuery(this).select();
					});
					
					return false;
				});
				
				jQuery('#form_fields').on('click', 'a.import', function() {
					
					jQuery('#form_fields textarea.export').hide();
					jQuery('#form_fields textarea.import').slideToggle('500', function() {
						jQuery(this).val('');
					});
					
					return false;
				});
				
			});
			</script>
	    <?php
	}
