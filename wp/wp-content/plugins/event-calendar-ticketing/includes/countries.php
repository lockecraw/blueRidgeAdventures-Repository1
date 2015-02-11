<?php

if ( !defined('ABSPATH') )
	die();
	
class IgniteWoo_Event_Countries {

	var $countries;

	var $states;


	function __construct() {
		global $woocommerce, $states;

		$this->countries = apply_filters('ignitewoo_event_countries', array(
			'AF' => __( 'Afghanistan', 'ignitewoo_events' ),
			'AX' => __( '&#197;land Islands', 'ignitewoo_events' ),
			'AL' => __( 'Albania', 'ignitewoo_events' ),
			'DZ' => __( 'Algeria', 'ignitewoo_events' ),
			'AD' => __( 'Andorra', 'ignitewoo_events' ),
			'AO' => __( 'Angola', 'ignitewoo_events' ),
			'AI' => __( 'Anguilla', 'ignitewoo_events' ),
			'AQ' => __( 'Antarctica', 'ignitewoo_events' ),
			'AG' => __( 'Antigua and Barbuda', 'ignitewoo_events' ),
			'AR' => __( 'Argentina', 'ignitewoo_events' ),
			'AM' => __( 'Armenia', 'ignitewoo_events' ),
			'AW' => __( 'Aruba', 'ignitewoo_events' ),
			'AU' => __( 'Australia', 'ignitewoo_events' ),
			'AT' => __( 'Austria', 'ignitewoo_events' ),
			'AZ' => __( 'Azerbaijan', 'ignitewoo_events' ),
			'BS' => __( 'Bahamas', 'ignitewoo_events' ),
			'BH' => __( 'Bahrain', 'ignitewoo_events' ),
			'BD' => __( 'Bangladesh', 'ignitewoo_events' ),
			'BB' => __( 'Barbados', 'ignitewoo_events' ),
			'BY' => __( 'Belarus', 'ignitewoo_events' ),
			'BE' => __( 'Belgium', 'ignitewoo_events' ),
			'PW' => __( 'Belau', 'ignitewoo_events' ),
			'BZ' => __( 'Belize', 'ignitewoo_events' ),
			'BJ' => __( 'Benin', 'ignitewoo_events' ),
			'BM' => __( 'Bermuda', 'ignitewoo_events' ),
			'BT' => __( 'Bhutan', 'ignitewoo_events' ),
			'BO' => __( 'Bolivia', 'ignitewoo_events' ),
			'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'ignitewoo_events' ),
			'BA' => __( 'Bosnia and Herzegovina', 'ignitewoo_events' ),
			'BW' => __( 'Botswana', 'ignitewoo_events' ),
			'BV' => __( 'Bouvet Island', 'ignitewoo_events' ),
			'BR' => __( 'Brazil', 'ignitewoo_events' ),
			'IO' => __( 'British Indian Ocean Territory', 'ignitewoo_events' ),
			'VG' => __( 'British Virgin Islands', 'ignitewoo_events' ),
			'BN' => __( 'Brunei', 'ignitewoo_events' ),
			'BG' => __( 'Bulgaria', 'ignitewoo_events' ),
			'BF' => __( 'Burkina Faso', 'ignitewoo_events' ),
			'BI' => __( 'Burundi', 'ignitewoo_events' ),
			'KH' => __( 'Cambodia', 'ignitewoo_events' ),
			'CM' => __( 'Cameroon', 'ignitewoo_events' ),
			'CA' => __( 'Canada', 'ignitewoo_events' ),
			'CV' => __( 'Cape Verde', 'ignitewoo_events' ),
			'KY' => __( 'Cayman Islands', 'ignitewoo_events' ),
			'CF' => __( 'Central African Republic', 'ignitewoo_events' ),
			'TD' => __( 'Chad', 'ignitewoo_events' ),
			'CL' => __( 'Chile', 'ignitewoo_events' ),
			'CN' => __( 'China', 'ignitewoo_events' ),
			'CX' => __( 'Christmas Island', 'ignitewoo_events' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'ignitewoo_events' ),
			'CO' => __( 'Colombia', 'ignitewoo_events' ),
			'KM' => __( 'Comoros', 'ignitewoo_events' ),
			'CG' => __( 'Congo (Brazzaville)', 'ignitewoo_events' ),
			'CD' => __( 'Congo (Kinshasa)', 'ignitewoo_events' ),
			'CK' => __( 'Cook Islands', 'ignitewoo_events' ),
			'CR' => __( 'Costa Rica', 'ignitewoo_events' ),
			'HR' => __( 'Croatia', 'ignitewoo_events' ),
			'CU' => __( 'Cuba', 'ignitewoo_events' ),
			'CW' => __( 'Cura&Ccedil;ao', 'ignitewoo_events' ),
			'CY' => __( 'Cyprus', 'ignitewoo_events' ),
			'CZ' => __( 'Czech Republic', 'ignitewoo_events' ),
			'DK' => __( 'Denmark', 'ignitewoo_events' ),
			'DJ' => __( 'Djibouti', 'ignitewoo_events' ),
			'DM' => __( 'Dominica', 'ignitewoo_events' ),
			'DO' => __( 'Dominican Republic', 'ignitewoo_events' ),
			'EC' => __( 'Ecuador', 'ignitewoo_events' ),
			'EG' => __( 'Egypt', 'ignitewoo_events' ),
			'SV' => __( 'El Salvador', 'ignitewoo_events' ),
			'GQ' => __( 'Equatorial Guinea', 'ignitewoo_events' ),
			'ER' => __( 'Eritrea', 'ignitewoo_events' ),
			'EE' => __( 'Estonia', 'ignitewoo_events' ),
			'ET' => __( 'Ethiopia', 'ignitewoo_events' ),
			'FK' => __( 'Falkland Islands', 'ignitewoo_events' ),
			'FO' => __( 'Faroe Islands', 'ignitewoo_events' ),
			'FJ' => __( 'Fiji', 'ignitewoo_events' ),
			'FI' => __( 'Finland', 'ignitewoo_events' ),
			'FR' => __( 'France', 'ignitewoo_events' ),
			'GF' => __( 'French Guiana', 'ignitewoo_events' ),
			'PF' => __( 'French Polynesia', 'ignitewoo_events' ),
			'TF' => __( 'French Southern Territories', 'ignitewoo_events' ),
			'GA' => __( 'Gabon', 'ignitewoo_events' ),
			'GM' => __( 'Gambia', 'ignitewoo_events' ),
			'GE' => __( 'Georgia', 'ignitewoo_events' ),
			'DE' => __( 'Germany', 'ignitewoo_events' ),
			'GH' => __( 'Ghana', 'ignitewoo_events' ),
			'GI' => __( 'Gibraltar', 'ignitewoo_events' ),
			'GR' => __( 'Greece', 'ignitewoo_events' ),
			'GL' => __( 'Greenland', 'ignitewoo_events' ),
			'GD' => __( 'Grenada', 'ignitewoo_events' ),
			'GP' => __( 'Guadeloupe', 'ignitewoo_events' ),
			'GT' => __( 'Guatemala', 'ignitewoo_events' ),
			'GG' => __( 'Guernsey', 'ignitewoo_events' ),
			'GN' => __( 'Guinea', 'ignitewoo_events' ),
			'GW' => __( 'Guinea-Bissau', 'ignitewoo_events' ),
			'GY' => __( 'Guyana', 'ignitewoo_events' ),
			'HT' => __( 'Haiti', 'ignitewoo_events' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'ignitewoo_events' ),
			'HN' => __( 'Honduras', 'ignitewoo_events' ),
			'HK' => __( 'Hong Kong', 'ignitewoo_events' ),
			'HU' => __( 'Hungary', 'ignitewoo_events' ),
			'IS' => __( 'Iceland', 'ignitewoo_events' ),
			'IN' => __( 'India', 'ignitewoo_events' ),
			'ID' => __( 'Indonesia', 'ignitewoo_events' ),
			'IR' => __( 'Iran', 'ignitewoo_events' ),
			'IQ' => __( 'Iraq', 'ignitewoo_events' ),
			'IE' => __( 'Republic of Ireland', 'ignitewoo_events' ),
			'IM' => __( 'Isle of Man', 'ignitewoo_events' ),
			'IL' => __( 'Israel', 'ignitewoo_events' ),
			'IT' => __( 'Italy', 'ignitewoo_events' ),
			'CI' => __( 'Ivory Coast', 'ignitewoo_events' ),
			'JM' => __( 'Jamaica', 'ignitewoo_events' ),
			'JP' => __( 'Japan', 'ignitewoo_events' ),
			'JE' => __( 'Jersey', 'ignitewoo_events' ),
			'JO' => __( 'Jordan', 'ignitewoo_events' ),
			'KZ' => __( 'Kazakhstan', 'ignitewoo_events' ),
			'KE' => __( 'Kenya', 'ignitewoo_events' ),
			'KI' => __( 'Kiribati', 'ignitewoo_events' ),
			'KW' => __( 'Kuwait', 'ignitewoo_events' ),
			'KG' => __( 'Kyrgyzstan', 'ignitewoo_events' ),
			'LA' => __( 'Laos', 'ignitewoo_events' ),
			'LV' => __( 'Latvia', 'ignitewoo_events' ),
			'LB' => __( 'Lebanon', 'ignitewoo_events' ),
			'LS' => __( 'Lesotho', 'ignitewoo_events' ),
			'LR' => __( 'Liberia', 'ignitewoo_events' ),
			'LY' => __( 'Libya', 'ignitewoo_events' ),
			'LI' => __( 'Liechtenstein', 'ignitewoo_events' ),
			'LT' => __( 'Lithuania', 'ignitewoo_events' ),
			'LU' => __( 'Luxembourg', 'ignitewoo_events' ),
			'MO' => __( 'Macao S.A.R., China', 'ignitewoo_events' ),
			'MK' => __( 'Macedonia', 'ignitewoo_events' ),
			'MG' => __( 'Madagascar', 'ignitewoo_events' ),
			'MW' => __( 'Malawi', 'ignitewoo_events' ),
			'MY' => __( 'Malaysia', 'ignitewoo_events' ),
			'MV' => __( 'Maldives', 'ignitewoo_events' ),
			'ML' => __( 'Mali', 'ignitewoo_events' ),
			'MT' => __( 'Malta', 'ignitewoo_events' ),
			'MH' => __( 'Marshall Islands', 'ignitewoo_events' ),
			'MQ' => __( 'Martinique', 'ignitewoo_events' ),
			'MR' => __( 'Mauritania', 'ignitewoo_events' ),
			'MU' => __( 'Mauritius', 'ignitewoo_events' ),
			'YT' => __( 'Mayotte', 'ignitewoo_events' ),
			'MX' => __( 'Mexico', 'ignitewoo_events' ),
			'FM' => __( 'Micronesia', 'ignitewoo_events' ),
			'MD' => __( 'Moldova', 'ignitewoo_events' ),
			'MC' => __( 'Monaco', 'ignitewoo_events' ),
			'MN' => __( 'Mongolia', 'ignitewoo_events' ),
			'ME' => __( 'Montenegro', 'ignitewoo_events' ),
			'MS' => __( 'Montserrat', 'ignitewoo_events' ),
			'MA' => __( 'Morocco', 'ignitewoo_events' ),
			'MZ' => __( 'Mozambique', 'ignitewoo_events' ),
			'MM' => __( 'Myanmar', 'ignitewoo_events' ),
			'NA' => __( 'Namibia', 'ignitewoo_events' ),
			'NR' => __( 'Nauru', 'ignitewoo_events' ),
			'NP' => __( 'Nepal', 'ignitewoo_events' ),
			'NL' => __( 'Netherlands', 'ignitewoo_events' ),
			'AN' => __( 'Netherlands Antilles', 'ignitewoo_events' ),
			'NC' => __( 'New Caledonia', 'ignitewoo_events' ),
			'NZ' => __( 'New Zealand', 'ignitewoo_events' ),
			'NI' => __( 'Nicaragua', 'ignitewoo_events' ),
			'NE' => __( 'Niger', 'ignitewoo_events' ),
			'NG' => __( 'Nigeria', 'ignitewoo_events' ),
			'NU' => __( 'Niue', 'ignitewoo_events' ),
			'NF' => __( 'Norfolk Island', 'ignitewoo_events' ),
			'KP' => __( 'North Korea', 'ignitewoo_events' ),
			'NO' => __( 'Norway', 'ignitewoo_events' ),
			'OM' => __( 'Oman', 'ignitewoo_events' ),
			'PK' => __( 'Pakistan', 'ignitewoo_events' ),
			'PS' => __( 'Palestinian Territory', 'ignitewoo_events' ),
			'PA' => __( 'Panama', 'ignitewoo_events' ),
			'PG' => __( 'Papua New Guinea', 'ignitewoo_events' ),
			'PY' => __( 'Paraguay', 'ignitewoo_events' ),
			'PE' => __( 'Peru', 'ignitewoo_events' ),
			'PH' => __( 'Philippines', 'ignitewoo_events' ),
			'PN' => __( 'Pitcairn', 'ignitewoo_events' ),
			'PL' => __( 'Poland', 'ignitewoo_events' ),
			'PT' => __( 'Portugal', 'ignitewoo_events' ),
			'QA' => __( 'Qatar', 'ignitewoo_events' ),
			'RE' => __( 'Reunion', 'ignitewoo_events' ),
			'RO' => __( 'Romania', 'ignitewoo_events' ),
			'RU' => __( 'Russia', 'ignitewoo_events' ),
			'RW' => __( 'Rwanda', 'ignitewoo_events' ),
			'BL' => __( 'Saint Barth&eacute;lemy', 'ignitewoo_events' ),
			'SH' => __( 'Saint Helena', 'ignitewoo_events' ),
			'KN' => __( 'Saint Kitts and Nevis', 'ignitewoo_events' ),
			'LC' => __( 'Saint Lucia', 'ignitewoo_events' ),
			'MF' => __( 'Saint Martin (French part)', 'ignitewoo_events' ),
			'SX' => __( 'Saint Martin (Dutch part)', 'ignitewoo_events' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'ignitewoo_events' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'ignitewoo_events' ),
			'SM' => __( 'San Marino', 'ignitewoo_events' ),
			'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'ignitewoo_events' ),
			'SA' => __( 'Saudi Arabia', 'ignitewoo_events' ),
			'SN' => __( 'Senegal', 'ignitewoo_events' ),
			'RS' => __( 'Serbia', 'ignitewoo_events' ),
			'SC' => __( 'Seychelles', 'ignitewoo_events' ),
			'SL' => __( 'Sierra Leone', 'ignitewoo_events' ),
			'SG' => __( 'Singapore', 'ignitewoo_events' ),
			'SK' => __( 'Slovakia', 'ignitewoo_events' ),
			'SI' => __( 'Slovenia', 'ignitewoo_events' ),
			'SB' => __( 'Solomon Islands', 'ignitewoo_events' ),
			'SO' => __( 'Somalia', 'ignitewoo_events' ),
			'ZA' => __( 'South Africa', 'ignitewoo_events' ),
			'GS' => __( 'South Georgia/Sandwich Islands', 'ignitewoo_events' ),
			'KR' => __( 'South Korea', 'ignitewoo_events' ),
			'SS' => __( 'South Sudan', 'ignitewoo_events' ),
			'ES' => __( 'Spain', 'ignitewoo_events' ),
			'LK' => __( 'Sri Lanka', 'ignitewoo_events' ),
			'SD' => __( 'Sudan', 'ignitewoo_events' ),
			'SR' => __( 'Suriname', 'ignitewoo_events' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'ignitewoo_events' ),
			'SZ' => __( 'Swaziland', 'ignitewoo_events' ),
			'SE' => __( 'Sweden', 'ignitewoo_events' ),
			'CH' => __( 'Switzerland', 'ignitewoo_events' ),
			'SY' => __( 'Syria', 'ignitewoo_events' ),
			'TW' => __( 'Taiwan', 'ignitewoo_events' ),
			'TJ' => __( 'Tajikistan', 'ignitewoo_events' ),
			'TZ' => __( 'Tanzania', 'ignitewoo_events' ),
			'TH' => __( 'Thailand', 'ignitewoo_events' ),
			'TL' => __( 'Timor-Leste', 'ignitewoo_events' ),
			'TG' => __( 'Togo', 'ignitewoo_events' ),
			'TK' => __( 'Tokelau', 'ignitewoo_events' ),
			'TO' => __( 'Tonga', 'ignitewoo_events' ),
			'TT' => __( 'Trinidad and Tobago', 'ignitewoo_events' ),
			'TN' => __( 'Tunisia', 'ignitewoo_events' ),
			'TR' => __( 'Turkey', 'ignitewoo_events' ),
			'TM' => __( 'Turkmenistan', 'ignitewoo_events' ),
			'TC' => __( 'Turks and Caicos Islands', 'ignitewoo_events' ),
			'TV' => __( 'Tuvalu', 'ignitewoo_events' ),
			'UG' => __( 'Uganda', 'ignitewoo_events' ),
			'UA' => __( 'Ukraine', 'ignitewoo_events' ),
			'AE' => __( 'United Arab Emirates', 'ignitewoo_events' ),
			'GB' => __( 'United Kingdom', 'ignitewoo_events' ),
			'US' => __( 'United States', 'ignitewoo_events' ),
			'UY' => __( 'Uruguay', 'ignitewoo_events' ),
			'UZ' => __( 'Uzbekistan', 'ignitewoo_events' ),
			'VU' => __( 'Vanuatu', 'ignitewoo_events' ),
			'VA' => __( 'Vatican', 'ignitewoo_events' ),
			'VE' => __( 'Venezuela', 'ignitewoo_events' ),
			'VN' => __( 'Vietnam', 'ignitewoo_events' ),
			'WF' => __( 'Wallis and Futuna', 'ignitewoo_events' ),
			'EH' => __( 'Western Sahara', 'ignitewoo_events' ),
			'WS' => __( 'Western Samoa', 'ignitewoo_events' ),
			'YE' => __( 'Yemen', 'ignitewoo_events' ),
			'ZM' => __( 'Zambia', 'ignitewoo_events' ),
			'ZW' => __( 'Zimbabwe', 'ignitewoo_events' )
		));

		// States set to array() are blank i.e. the country has no use for the state field.
		$states = array(
			'AF' => array(),
			'AT' => array(),
			'BE' => array(),
			'BI' => array(),
			'CZ' => array(),
			'DE' => array(),
			'DK' => array(),
			'FI' => array(),
			'FR' => array(),
			'HU' => array(),
			'IS' => array(),
			'IL' => array(),
			'KR' => array(),
			'NL' => array(),
			'NO' => array(),
			'PL' => array(),
			'PT' => array(),
			'SG' => array(),
			'SK' => array(),
			'SI' => array(),
			'LK' => array(),
			'SE' => array(),
			'VN' => array(),
		);

		foreach ( $this->countries as $CC => $country )
			if ( ! isset( $states[ $CC ] ) && file_exists( dirname( __FILE__ ) . '/states/' . $CC . '.php' ) )
				include( dirname( __FILE__ ) . '/states/' . $CC . '.php' );

		$this->states = apply_filters( 'ignitewoo_events_states', $states );
		
	}

	
	public function get_states( $cc ) {
	
		if ( isset( $this->states[ $cc ] ) ) 
			return $this->states[ $cc ];
	
	}
	
	
	function country_dropdown_options( $selected_country = '', $selected_state = '', $escape = false ) {

		if ( apply_filters('woocommerce_sort_countries', true ) )
			asort( $this->countries );

		if ( $this->countries ) 
		foreach ( $this->countries as $key => $value ) :
		
			if ( $states =  $this->get_states( $key ) ) :
			
				echo '<optgroup label="' . $value . '">';
				
    				foreach ( $states as $state_key=>$state_value ) :
    				
    					echo '<option value="'.$key.':'.$state_key.'"';

    					if ( $selected_country == $key && $selected_state == $state_key ) 
						echo ' selected="selected"';

    					echo '>' . $value . ' &mdash; ' . ( $escape ? esc_js( $state_value ) : $state_value ) . '</option>';
    					
    				endforeach;
    				
				echo '</optgroup>';
			else :
			
				echo '<option';
				if ( $selected_country == $key && '*' == $selected_state ) echo ' selected="selected"';
				echo ' value="' . $key . '">'. ( $escape ? esc_js( $value ) : $value ) . '</option>';
				
			endif;
			
		endforeach;
	}
}
