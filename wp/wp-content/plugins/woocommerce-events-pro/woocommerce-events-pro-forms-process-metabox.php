<?php

	function process_forms_meta_box( $post_id, $post ) { 

		// Save fields as serialised array
		$form_fields = array();

		if ( isset( $_POST['addon_name'] ) ) :

			$field_name		= $_POST['addon_name'];
			$field_description	= $_POST['addon_description'];
			$field_type 		= $_POST['addon_type'];
			$field_option_label	= $_POST['addon_option_label'];
			$field_option_price	= $_POST['addon_option_price'];
			$field_position 	= $_POST['addon_position'];
			$field_required		= $_POST['addon_required'];
    
			for ( $i=0; $i< sizeof( $field_name ); $i++ ) :
				
				if ( !isset( $field_name[$i] ) || ( '' == $field_name[$i] ) ) 
					continue;

				// Meta
				$field_options 	= array();
				$option_label 	= $field_option_label[$i];
				$option_price 	= $field_option_price[$i];
				
				for ( $ii=0; $ii < sizeof( $option_label ); $ii++ ) :
					$label = esc_attr(stripslashes( $option_label[$ii] ) );
					$price = esc_attr(stripslashes( $option_price[$ii] ) );

					$field_options[] = array(
						'label' => $label,
						'price' => $price
					);

				endfor;
				
				if ( 0 == sizeof( $field_options ) ) continue; // Needs options
				
				// Add to array	 	
				$form_fields[] = array(
					'name' 		=> esc_attr(stripslashes( $field_name[$i] ) ),
					'description' 	=> esc_attr(stripslashes( $field_description[$i] ) ),
					'type' 		=> esc_attr(stripslashes( $field_type[$i] ) ),
					'position'	=> (int) $field_position[$i],
					'options' 	=> $field_options,
					'required'	=> ( isset( $field_required[$i] ) ) ? 1 : 0
				);
			    
			endfor; 
		endif;	

		if ( !function_exists('addons_cmp' ) ) {

			function addons_cmp( $a, $b) {
			    if ( $a['position'] == $b['position'] ) {
				return 0;
			    }
			    return ( $a['position'] < $b['position'] ) ? -1 : 1;
			}

		}

		uasort( $form_fields, 'addons_cmp' );
		
		if ( $_POST['import_product_addon'] ) {

			$import_addons = maybe_unserialize( maybe_unserialize( stripslashes( trim( $_POST['import_product_addon'] ) ) ) );

			if ( is_array( $import_addons ) && sizeof( $import_addons ) > 0 ) {

				$valid = true;

				foreach ( $import_addons as $addon ) {

					if ( !isset( $addon['name'] ) || !$addon['name'] ) $valid = false;
					if ( !isset( $addon['description'] ) ) $valid = false;
					if ( !isset( $addon['type'] ) ) $valid = false;
					if ( !isset( $addon['position'] ) ) $valid = false;
					if ( !isset( $addon['options'] ) ) $valid = false;
					if ( !isset( $addon['required'] ) ) $valid = false;

				}

				if ( $valid) {

					// Append data
					$form_fields = array_merge( $form_fields, $import_addons );
				}
			}
		}

		update_post_meta( $post_id, '_form_fields', $form_fields );

	}
