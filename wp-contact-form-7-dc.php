<?php
/*
Plugin Name: Contact Form 7 - Dynamic Content from ACF
Description: Extend Contact Form 7 with dynamic content from different ACF sources
Author: Dominic Vogl
Author URI: https://github.com/dominicvogl/
Text Domain: contact-form-7-dc
Domain Path: /languages/
Version: 0.0.2
*/


// if this file is called directly, DIE!
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


/**
 * This wil add dynamic data to hidden field: dynamic-content
 */
class Cf7_Add_Dynamic_Value {
	/**
	 * Holds contact form 7 property
	 */
	protected $contact_form;


	//main constructor
	public function __construct() {
		add_filter( 'wpcf7_posted_data', array( $this, 'add_dynamic_data' ) );
		add_filter( 'wpcf7_mail_tag_replaced', array( $this, 'format_line_breaks' ), 10, 4 );
	}


	/**
	 * @hooked wpcf7_posted_data
	 *
	 * @param array $posted_data
	 *
	 * @since 0.0.1
	 * @version 0.0.2
	 * @author Dominic Vogl
	 */

	public function add_dynamic_data( $posted_data ) {

		// collect Radio results for returning
		if ( isset( $posted_data['radio'] ) ) {
			$radioResults = [];
			foreach ( $posted_data['radio']['values'] as $radio ) {
				$radioResults[] = $radio[0];
			}
		}

		if ( isset( $posted_data['checkbox'] ) ) {
			// collecting checkbox results
			$checkboxResults = [];
			foreach ( $posted_data['checkbox']['values'] as $checkboxGroup ) {
				foreach ( $checkboxGroup as $checkboxValue ) {
					$checkboxResults[] = $checkboxValue;
				}
			}
		}

		$posted_data['dynamic-radios']     = implode( ';\n', $radioResults );
		$posted_data['dynamic-checkboxes'] = implode( ';\n ', $checkboxResults );

		$posted_data['dynamic-debug'] = json_encode( $posted_data );

		return $posted_data;
	}

	/**
	 * @param $replaced
	 * @param $submitted
	 * @param $html
	 * @param $mail_tag
	 *
	 * @hooked wpcf7_mail_tag_replaced
	 *
	 * transform break signs from dynamic fields above to html line breaks
	 *
	 * @return array|mixed|string|string[]
	 * @since 0.0.2
	 * @author Dominic Vogl
	 */

	public function format_line_breaks( $replaced, $submitted, $html, $mail_tag ) {

		$fields = [ 'dynamic-checkboxes', 'dynamic-radios' ];

		if ( in_array( $mail_tag->field_name(), $fields ) && $html ) {
			$replaced = str_replace( ';\n', '<br>', $submitted );
		}

		return $replaced;
	}


}

new Cf7_Add_Dynamic_Value();
