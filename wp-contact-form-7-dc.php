<?php
/*
Plugin Name: Contact Form 7 - Dynamic Content from ACF
Description: Extend Contact Form 7 with dynamic content from different ACF sources
Author: Dominic Vogl
Author URI: https://github.com/dominicvogl/
Text Domain: contact-form-7-dc
Domain Path: /languages/
Version: 0.0.1
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
	}

	/**
	 * @hooked wpcf7_posted_data
	 * @param array $posted_data
	 */
	public function add_dynamic_data( $posted_data ) {

		$quote = '';
		$your_name = isset( $posted_data[ 'company-name' ] ) ? sanitize_text_field( $posted_data[ 'company-name' ]  ) : '';
		$your_email = isset( $posted_data[ 'client-id' ] ) ? sanitize_text_field( $posted_data[ 'client-id' ]  ) : '';
		$your_subject = isset( $posted_data[ 'yourmail' ] ) ? sanitize_text_field( $posted_data[ 'yourmail' ]  ) : '';
		$radios = isset( $posted_data[ 'radio-147-0' ] ) ? sanitize_text_field( $posted_data[ 'radio-147-0' ]  ) : '';
		$radios = isset( $posted_data[ 'radio-[values]' ] ) ? sanitize_text_field( $posted_data[ 'radio-147-1' ]  ) : '';
		//you can fillup this value as per your requirement. Replace dynami-content with the hidden name added on your form

		// collect Radio results for returning
		if(isset($posted_data['radio'])) {
			$radioResults = [];
			foreach($posted_data['radio']['values'] as $radio) {
				$radioResults[] = $radio[0];
			}
		}

		if(isset($posted_data['checkbox'])) {
			// collecting checkbox results
			$checkboxResults = [];
			foreach($posted_data['checkbox']['values'] as $checkboxGroup) {
				foreach($checkboxGroup as $checkboxValue) {
					$checkboxResults[] = $checkboxValue;
				}
			}
		}

		$posted_data[ 'dynamic-radios' ] = implode('; ', $radioResults);
		$posted_data[ 'dynamic-checkboxes'] = implode('; ', $checkboxResults);

		$posted_data['dynamic-debug'] = json_encode($posted_data);

		return $posted_data;
	}


}

new Cf7_Add_Dynamic_Value();
