<?php 
class IgniteWoo_Events_Pro_Cart_Helpers { 

	function __construct() { 

		add_action( 'woocommerce_before_add_to_cart_button', array( &$this, 'event_form_fields_position_button' ), 10 );

		add_action( 'woocommerce_after_add_to_cart_button', array( &$this, 'event_form_fields_position_button_after' ), 10 );

		add_action( 'woocommerce_after_single_product_summary', array( &$this, 'event_form_fields_position_desc' ), 15 );

		add_filter( 'woocommerce_add_cart_item_data', array( &$this, 'add_cart_item_data' ) );

		add_filter( 'woocommerce_get_cart_item_from_session', array( &$this, 'get_cart_item_from_session' ), 10, 2 );

		add_filter( 'woocommerce_get_item_data', array( &$this, 'get_item_data' ), 10, 2 );

		add_filter( 'woocommerce_add_cart_item', array( &$this, 'add_cart_item' ), 10, 1 );

		add_action( 'woocommerce_add_order_item_meta', array( &$this, 'add_order_item_meta' ), 10, 2 );
		
		add_filter( 'woocommerce_add_to_cart_validation', array( &$this, 'validate_add_cart_item' ), 10, 3 );

		add_filter( 'add_to_cart_text', array( &$this, 'add_to_cart_text' ), 10, 1 );

		add_filter( 'woocommerce_add_to_cart_url', array( &$this, 'add_to_cart_url' ), 10, 1 );
		
	}

	function event_form_fields_position_button() { 
		global $ignitewoo_events;

		$settings = $ignitewoo_events->get_post_data(); 

		if ( !isset( $settings['event_form_position'] ) )
			return;
			
		if ( 'before_button' != $settings['event_form_position'] ) 
			return;

		$this->event_form_fields();
	}


	function event_form_fields_position_button_after() { 
		global $ignitewoo_events;

		$settings = $ignitewoo_events->get_post_data(); 

		if ( !isset( $settings['event_form_position'] ) )
			return;
			
		if ( 'after_button' != $settings['event_form_position'] ) 
			return;

		$this->event_form_fields();
	}


	function event_form_fields_position_desc() { 
		global $ignitewoo_events;

		$settings = $ignitewoo_events->get_post_data(); 
	
		if ( !isset( $settings['event_form_position'] ) )
			return;
			
		if ( 'after_description' != $settings['event_form_position'] ) 
			return;

		echo '<div class="event_form_wrapper">';

		$this->event_form_fields();

		echo '</div>';
	}


	function event_form_fields() {
		global $post, $ignitewoo_events;

		$settings = $ignitewoo_events->get_post_data(); 

		if ( !isset( $settings['event_form'] ) || count( $settings['event_form'] ) <= 0 )
			 return;

		foreach( $settings['event_form'] as $f ) { 

			$event_fields = get_post_meta( absint( $f ), '_form_fields', true );

			if ( is_array( $event_fields ) && sizeof( $event_fields) > 0 ) { 

				if ( isset( $settings['event_form_show_title'] ) && 'yes' == $settings['event_form_show_title'] ) 
					echo '<h3 class="event_form_title">' . get_the_title( $settings['event_form'] ) . '</h3>';

				foreach ( $event_fields as $addon ) {
						
					if ( !isset( $addon['name'] ) ) 
						continue;
					
					?>
					<div class="event-addon event-addon-<?php echo sanitize_title( $addon['name'] ); ?>">
					
						<?php 
							if ( $addon['name'] ) { ?>
								<h3>
									<?php echo wptexturize( $addon['name'] ); ?> <?php if ( $addon['type']=='file_upload' ) echo sprintf(__('(max size %s)', 'ignitewoo_events' ), $this->max_upload_size() ); ?>
								</h3>
							<?php } ?>
							
						<?php if ( $addon['description'] ) { ?>
							<p><?php echo wptexturize( $addon['description'] ); ?></p>
						<?php } ?>
					
						<?php
						switch ( $addon['type'] ) {
						
							case "checkbox" :
								foreach ( $addon['options'] as $option ) {
								
									$current_value = (
										isset( $_POST['addon-'. sanitize_title( $addon['name'] )] ) && 
										in_array(sanitize_title( $option['label'] ), $_POST['addon-'. sanitize_title( $addon['name'] )] )
										) ? 1 : 0;
									
									$price = ( $option['price']>0) ? ' (' . woocommerce_price( $option['price'] ) . ' )' : '';
									
									echo '<p class="form-row form-row-wide"><label><input type="checkbox" name="addon-'. sanitize_title( $addon['name'] ) .'[]" value="'. sanitize_title( $option['label'] ) .'" '.checked( $current_value, 1, false).' /> '. wptexturize( $option['label'] ) . $price .'</label></p>';
									
								}
								break;
								
							case "radiobutton" :
							
								foreach ( $addon['options'] as $option ) {								
									$current_value = (
										isset( $_POST['addon-'. sanitize_title( $addon['name'] )] ) && 
										in_array(sanitize_title( $option['label'] ), $_POST['addon-'. sanitize_title( $addon['name'] )] )
										) ? 1 : 0;
									
									$price = ( $option['price']>0) ? ' (' . woocommerce_price( $option['price'] ) . ' )' : '';
									
									echo '<p class="form-row form-row-wide"><label><input type="radio" name="addon-'. sanitize_title( $addon['name'] ) .'[]" value="'. sanitize_title( $option['label'] ) .'" '.checked( $current_value, 1, false).' /> '. wptexturize( $option['label'] ) . $price .'</label></p>';
									
								}
								break;
							case "select" :
							
								$current_value = ( isset( $_POST['addon-'. sanitize_title( $addon['name'] )] ) ) ? $_POST['addon-'. sanitize_title( $addon['name'] )] : '';
								
								echo '<p class="form-row form-row-wide"><select name="addon-'. sanitize_title( $addon['name'] ) .'">';
								
								if ( ! isset( $addon['required'] ) )
									echo '<option value="">'. __('None', 'ignitewoo_events' ) .'</option>';
								else 
									echo '<option value="">'. __('Select an option...', 'ignitewoo_events' ) .'</option>';
									
								$loop = 0;
								
								foreach ( $addon['options'] as $option) {
									$loop++;
									
									$price = ( $option['price']>0) ? ' (' . woocommerce_price( $option['price'] ) . ' )' : '';
									
									echo '<option value="'. sanitize_title( $option['label'] ) .'-'. $loop .'" '.selected( $current_value, sanitize_title( $option['label'] ), false).'>'. wptexturize( $option['label'] ) . $price .'</option>';
									
								}
								
								echo '</select></p>';
								
								break;
								
							case "custom" :
							
								foreach ( $addon['options'] as $option) {
									
									$current_value = ( isset( $_POST['addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] )] ) ) ? $_POST['addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] )] : '';
									
									$price = ( $option['price']>0) ? ' (' . woocommerce_price( $option['price'] ) . ' )' : '';

									if ( empty( $option['label'] ) ) {
										echo '<p class="form-row form-row-wide"><input type="text" class="input-text" name="addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] ) .'" value="'.$current_value.'" /></p>';
									} else {
										echo '<p class="form-row form-row-wide"><label>' . $price .' <input type="text" class="input-text" name="addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] ) .'" value="'.$current_value.'" /></label></p>';
									}
								
								}
								
								break;
								
							case "custom_textarea" :
							
								foreach ( $addon['options'] as $option) {
									
									$current_value = ( isset( $_POST['addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] )] ) ) ? $_POST['addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] )] : '';
									
									$price = ( $option['price']>0) ? ' (' . woocommerce_price( $option['price'] ) . ' )' : '';

									if ( empty( $option['label'] ) ) {
									
										echo '<p class="form-row form-row-wide"><textarea class="textarea input-text" name="addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] ) .'" rows="4" cols="20">'. esc_textarea( $current_value) .'</textarea></p>';
										
									} else {
									
										echo '<p class="form-row form-row-wide"><label>'. wptexturize( $option['label'] ) . $price .': <textarea class="textarea input-text" name="addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] ) .'" rows="4" cols="20">'. esc_textarea( $current_value) .'</textarea></label></p>';
									}
								
								}
								
								break;

							case "file_upload" :
							
								foreach ( $addon['options'] as $option) {
								
									$price = ( $option['price']>0) ? ' (' . woocommerce_price( $option['price'] ) . ' )' : '';									
									
									if ( empty( $option['label'] ) ) {
									
										echo '<p class="form-row form-row-wide"><input type="file" class="input-text" name="addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] ) .'" /></p>';
										
									} else {
									
										echo '<p class="form-row form-row-wide"><label>'. wptexturize( $option['label'] ) . $price .': <input type="file" class="input-text" name="addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] ) .'" /></label></p>';
									}
								
								}
							break;

						}
						?>
						
						<div class="clear"></div>
						
					</div>
					
					<?php
					
				}
			}
		}
	}


	function add_cart_item_data( $cart_item_meta, $product_id = '', $variation_id = '' ) {
		global $woocommerce, $ignitewoo_events;

		$settings = $ignitewoo_events->get_post_data();

		if ( !$settings && isset( $_GET['add-to-cart'] ) && absint( $_GET['add-to-cart'] ) > 0 ) { 

			$settings = get_post_meta( absint( $_GET['add-to-cart'] ), '_ignitewoo_event_info', true );

		}
		
		if ( empty( $settings ) )
			return $cart_item_meta;
			

		$cart_item_meta['event_addons'] = array();

		if ( isset( $settings['event_form'] ) && is_array( $settings['event_form'] ) && count( $settings['event_form'] ) > 0 )
		foreach( $settings['event_form'] as $key => $event_form_id ) { 

			$event_fields = get_post_meta( $event_form_id, '_form_fields', true );

			if ( is_array( $event_fields ) && sizeof( $event_fields ) > 0 ) 
			foreach ( $event_fields as $addon ) {
						
				if ( !isset( $addon['name'] ) ) continue;

				switch ( $addon['type'] ) {
				
					case "checkbox" :
					case "radiobutton" :
						
						$posted = ( isset( $_POST['addon-' . sanitize_title( $addon['name'] )]  ) ) ? $_POST['addon-' . sanitize_title( $addon['name'] )] : '';
						
						if ( !$posted || sizeof( $posted ) == 0 ) 
							continue;
						
						foreach ( $addon['options'] as $option ) {
							
							if ( array_search( sanitize_title( $option['label'] ), $posted) !== FALSE ) {
								
								// Set
								$cart_item_meta['event_addons'][] = array(
									'name' 		=> esc_attr( $addon['name'] ),
									'value'		=> esc_attr( $option['label'] ),
									'price' 	=> esc_attr( $option['price'] )
								);
								
							}

						}
						
						break;
						
					case "select" :
						
						$posted = ( isset( $_POST['addon-' . sanitize_title( $addon['name'] )]  ) ) ? $_POST['addon-' . sanitize_title( $addon['name'] )] : '';
						
						if ( !$posted ) 
							continue;
						
						$chosen_option = '';
						
						$loop = 0;
						
						foreach ( $addon['options'] as $option ) {
						
							$loop ++;
						
							if ( sanitize_title( $option['label'] . '-' . $loop )==$posted) {
							
								$chosen_option = $option;
								
								break;
							}
						}
						
						if ( !$chosen_option )
							continue;
						
						$cart_item_meta['event_addons'][] = array(
							'name' 		=> esc_attr( $addon['name'] ),
							'value'		=> esc_attr( $chosen_option['label'] ),
							'price' 	=> esc_attr( $chosen_option['price'] )
						);	
						
						break;
						
					case "custom" :
					case "custom_textarea" :
						
						foreach ( $addon['options'] as $option ) {
							
							$posted = ( isset( $_POST['addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] )]  ) ) ? $_POST['addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] )] : '';
							
							if ( !$posted) 
								continue;

							$label = !empty( $option['label'] ) ? $option['label'] : $addon['name'];
			
							$cart_item_meta['event_addons'][] = array(
								'name' 		=> esc_attr( $label ),
								'value'		=> esc_attr( stripslashes( trim( $posted ) ) ),
								'price' 	=> esc_attr( $option['price'] )
							);								
							
						}
						
						break;
						
					case "file_upload" :
					
						//WordPress Administration File API 
						require_once( ABSPATH . 'wp-admin/includes/file.php' );
						
						//WordPress Media Administration API */
						require_once( ABSPATH . 'wp-admin/includes/media.php' );
			
						add_filter( 'upload_dir',  array( &$this, 'upload_dir' ) );
						
						foreach ( $addon['options'] as $option ) {
								
							$field_name = 'addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] );
							
							if ( isset( $_FILES[$field_name] ) && !empty( $_FILES[$field_name]) && !empty( $_FILES[$field_name]['name'] ) ) {

								$file   = $_FILES[$field_name];

								$upload = wp_handle_upload( $file, array('test_form' => false ) );

								if( !isset( $upload['error']) && isset( $upload['file'] ) ) {
							    
									$file_path = $upload['url'];
									
									$cart_item_meta['event_addons'][] = array(
											    'name' => esc_attr( $option['label'] ),
											    'value' => esc_attr( stripslashes( trim( $file_path ) ) ),
											    'display' => basename( esc_attr( stripslashes( trim( $file_path ) ) ) ),
											    'price' 	=> esc_attr( $option['price'] )
										    );
									
								} else {
									
									$woocommerce->add_error( $upload['error'] );
									
								    }
							}
														
						}
							
						remove_filter( 'upload_dir',  array(&$this, 'upload_dir' ) );
					break;
				}
																
			}
		}

		return $cart_item_meta;

	}
	
	function get_cart_item_from_session( $cart_item, $values ) {
		
		if ( isset( $values['event_addons'] ) ) {
		
			$cart_item['event_addons'] = $values['event_addons'];
		
			$cart_item = $this->add_cart_item( $cart_item );
		}
		
		return $cart_item;
		
	}
	
	function get_item_data( $item_data, $cart_item ) {

		if ( isset( $cart_item['event_addons'] ) ) {
			
			foreach ( $cart_item['event_addons'] as $addon ) {
				
				$name = $addon['name'];
				
				if ( $addon['price'] > 0 ) $name .= ' (' . woocommerce_price( $addon['price']) . ')';
				
				$item_data[] = array(
					'name' => $name,
					'value' => $addon['value'],
					'display' => isset( $addon['display'] ) ? $addon['display'] : ''
				);
				
			}
			
		}

		return $item_data;
			
	}
	
	function add_cart_item( $cart_item ) {

		if ( isset( $cart_item['event_addons'] ) ) {
			
			$extra_cost = 0;
			
			foreach ( $cart_item['event_addons'] as $addon) {
				
				if ( $addon['price'] > 0 ) $extra_cost += $addon['price'];
				
			}
			
			$cart_item['data']->adjust_price( $extra_cost );
			
		}

		return $cart_item;
		
	}
	
	function add_order_item_meta( $item_id, $cart_item ) {

		if ( isset( $cart_item['event_addons'] ) ) {
			
			foreach ( $cart_item['event_addons'] as $addon ) {
				
				$name = $addon['name'];
				
				if ( $addon['price'] > 0 ) $name .= ' (' . strip_tags( woocommerce_price( $addon['price'] ) ) . ')';

				woocommerce_add_order_item_meta( $item_id, $name, $addon['value'] );
	
			}

		}
	
	}
	
	
	function validate_add_cart_item( $passed, $product_id, $qty ) {
		global $woocommerce, $ignitewoo_events;

		$settings = $ignitewoo_events->get_post_data( $product_id );

		if ( !$settings && isset( $_GET['add-to-cart'] ) && absint( $_GET['add-to-cart'] ) > 0 ) { 

			$settings = get_post_meta( absint( $_GET['add-to-cart'] ), '_ignitewoo_event_info', true );

		} else if ( isset( $_GET['product_id'] ) && absint( $_GET['product_id'] ) > 0 ) {

			$settings = get_post_meta( absint( $_GET['product_id'] ), '_ignitewoo_event_info', true );

		}

		if ( !empty( $settings['event_form'] ) )
			$event_fields = get_post_meta( $settings['event_form'][0], '_form_fields', true );
		else 
			$event_fields = '';

		if ( is_array( $event_fields ) && sizeof( $event_fields ) > 0 ) 
		foreach ( $event_fields as $addon ) {
					
			if ( !isset( $addon['name'] ) ) 
				continue;

			if ( !isset( $addon['required'] ) ) 
				continue;
			
			if ( $addon['required'] ) {
			
				switch ( $addon['type']) :
					case "checkbox" :
					case "radiobutton" :
					case "select" :
						
						$posted = ( isset( $_POST['addon-' . sanitize_title( $addon['name'] )]  ) ) ? $_POST['addon-' . sanitize_title( $addon['name'] )] : '';
						
						if ( !$posted || sizeof( $posted)==0) $passed = false;
						
					break;
					case "custom" :
					case "custom_textarea" :
						
						foreach ( $addon['options'] as $option) {
							
							$posted = ( isset( $_POST['addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] )]  ) ) ? $_POST['addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] )] : '';
							
							if ( !$posted || sizeof( $posted)==0) {
								$passed = false;
								break;
							}							
						}
						
					break;
					case "file_upload" :
						
						foreach ( $addon['options'] as $option) {
							
							$field_name = 'addon-' . sanitize_title( $addon['name'] ) . '-' . sanitize_title( $option['label'] );
							
							if ( !isset( $_FILES[$field_name] ) || empty( $_FILES[$field_name]) || empty( $_FILES[$field_name]['name'] ) ) {
								$passed = false;
								break;
							}
														
						}

					break;
				endswitch;
																
				if ( !$passed) {
					$woocommerce->add_error( sprintf( __('"%s" is a required field.', 'ignitewoo_events'), $addon['name']) );
					break;
				}
			}
			
			do_action('woocommerce_validate_posted_addon_data', $addon);
															
		}

		return $passed;
	}

	function check_required_event_addons( $product_id ) {

		$event_addons = get_post_meta( $product_id, '_product_event_addons', true );

		if ( $event_addons && !empty( $event_addons ) ) {
			foreach ( $event_addons as $addon ) {
				if ( '1' == $addon['required'] ) {
					return true;
				}
			}
		}

		return false;
	}

	function add_to_cart_text( $text ) {
		global $product;

		if ( !is_single( $product->id ) ) {
		
			if ( $this->check_required_event_addons( $product->id ) ) {
			
				$product->product_type = 'event_addons';
				
				$text = apply_filters( 'event_addons_add_to_cart_text', __( 'Select options', 'ignitewoo_events' ) );
			}
		}

		return $text;
	}

	function add_to_cart_url( $url ) {
		global $product;

		if ( ! is_single( $product->id ) ) {
		
			if ( $this->check_required_event_addons( $product->id ) ) {
			
				$product->product_type = 'event_addons';
				
				$url = get_permalink( $product->id );
			}
		}

		return $url;
	}

	function max_upload_size() {
	
		$u_bytes = $this->convert_hr_to_bytes( ini_get( 'upload_max_filesize' ) );
		
		$p_bytes = $this->convert_hr_to_bytes( ini_get( 'post_max_size' ) );
		
		$bytes = min( $u_bytes, $p_bytes);
		
		return $this->convert_bytes_to_hr( $bytes );
	}

	function convert_hr_to_bytes( $size ) {
		$size = strtolower( $size);
		
		$bytes = (int) $size;
		
		if ( strpos( $size, 'k') !== false )
			$bytes = intval( $size) * 1024;
			
		elseif ( strpos( $size, 'm') !== false )
			$bytes = intval( $size) * 1024 * 1024;
			
		elseif ( strpos( $size, 'g') !== false )
			$bytes = intval( $size) * 1024 * 1024 * 1024;
			
		return $bytes;
	}
	
	function convert_bytes_to_hr( $bytes ) {
	
		$units = array( 0 => 'B', 1 => 'kB', 2 => 'MB', 3 => 'GB' );
		
		$log = log( $bytes, 1024 );
		
		$power = (int) $log;
		
		$size = pow(1024, $log - $power);
		
		return $size . $units[$power];
	}
	
	function upload_dir( $pathdata ) {
	
		$subdir = '/ignitewoo_events_uploads/'.md5(session_id( ) );
		
		$pathdata['path'] = str_replace( $pathdata['subdir'], $subdir, $pathdata['path']);
		
		$pathdata['url'] = str_replace( $pathdata['subdir'], $subdir, $pathdata['url']);
		
		$pathdata['subdir'] = str_replace( $pathdata['subdir'], $subdir, $pathdata['subdir']);
		
		return $pathdata;
	}

}