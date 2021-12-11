<?php

namespace deliveryplugin\Ukrposhta\classes;

if ( ! defined('ABSPATH')) {
  exit;
}

class CheckoutValidator
{

  public function __construct()
  {
    add_action('woocommerce_checkout_process', [ $this, 'validateFields' ]);
    add_filter('woocommerce_checkout_fields', [ $this, 'removeDefaultFieldsFromValidation' ]);
    add_filter('woocommerce_checkout_posted_data', [ $this, 'processCheckoutPostedData' ]);
    // Hide Ship to different address for Ukraine
    if ( get_option( 'woocommerce_default_country' ) == 'UA' ) {
      add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );
    } else {
      add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
    }
  }

  public function removeDefaultFieldsFromValidation($fields)
  {
    if ($this->maybeDisableDefaultFields()) {
      unset($fields['billing']['billing_address_1']);
      unset($fields['billing']['billing_address_2']);
      unset($fields['billing']['billing_city']);
      unset($fields['billing']['billing_state']);
      unset($fields['billing']['billing_postcode']);
    }

    return $fields;
  }

  public function validateFields()
  {
    if (isset($_POST['shipping_method'])) {
      if (preg_match('/^' . MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '.*/i', $_POST['shipping_method'][0])) {

        $default_country_UA = ( get_option( 'woocommerce_default_country' ) == 'UA') ? true : false;
        $billing_country = isset( $_POST['billing_country']) ? $_POST['billing_country'] : '';
        // Ukraine
        if ( $billing_country == 'UA' ) {
					if (( ! $_POST['billing_address_1'] || ! $_POST['billing_city'] || ! $_POST['billing_phone']) &&
					     /* ! $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_custom_address'] &&*/ (
					      ! $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_city'] ||
					      ! $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_warehouse'] 
					  )) {
					  wc_add_notice('Заповніть інформацію про доставку', 'error');
					} elseif ( strlen( $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_city'] ) < 2 ) {
					  wc_add_notice('Назва населеного пункту закоротка', 'error');
					}

          if ( ! preg_match( '/^\d{5}$/', $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_warehouse'] ) &&
            $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_warehouse'] ) {
              wc_add_notice('Поштовий індекс в Україні складається з 5 цифр', 'error');
          }
          
          if ( preg_match( '/\d/', $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_city'] ) && 
            $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_city'] ) {
              wc_add_notice('Назва населеного пункту невірна', 'error');
          }

          // Validates city name to match its postcode with Nova Poshta API
          $city_by_postcode_UA = $this->getCityByPostcode( $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_warehouse'], 'UA' );
          $city_by_postcode_RU = $this->getCityByPostcode( $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_warehouse'], 'RU' );
          $city_by_postcode_EN = $this->getCityByPostcode( $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_warehouse'], 'EN' );
          $input_city = $_POST[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_city'];
          if ( strpos( $input_city, "'" ) !== false ) {
              $input_city = str_replace( "\\", "", $input_city );
          }
          $input_city_tolower = wc_strtolower( $input_city );
          $input_city_tolower_ucf = mb_convert_case( $input_city_tolower, MB_CASE_TITLE,"UTF-8" );

          if ( in_array( $input_city_tolower_ucf, $city_by_postcode_UA ) ||
               in_array( $input_city_tolower_ucf, $city_by_postcode_RU ) ||
               in_array( $input_city_tolower_ucf, $city_by_postcode_EN ) ) {
            // Є такий поштовий індекс - нічого не робимо
          } else {
              wc_add_notice('Назва населеного пункту не відповідає введеному поштовому індексу', 'error');
          }          
        }      
      }
    }
  }

  public function processCheckoutPostedData($data)
  { 
    add_filter( 'woocommerce_ship_to_different_address_checked', '__return_false' );
	  if (isset($data['shipping_method'])) {
		  if (
		  	preg_match('/^' . MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '.*/i', $data['shipping_method'][0]) &&
			  isset($_POST['ship_to_different_address'])
		  ) {
		  	unset($data['ship_to_different_address']); 
		  	unset($data['shipping_first_name']);
			  unset($data['shipping_last_name']);
			  unset($data['shipping_company']);
			  unset($data['shipping_country']);
			  unset($data['shipping_address_1']);
			  unset($data['shipping_address_2']);
			  unset($data['shipping_city']);
			  unset($data['shipping_state']);
			  unset($data['shipping_postcode']);
		  }
	  }

  	return $data;
  }

  private function maybeDisableDefaultFields()
  {
    return isset($_POST['shipping_method']) &&
      preg_match('/^' . MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '.*/i', $_POST['shipping_method'][0]) &&
      apply_filters('morkva_ukrposhta_prevent_disable_default_fields', false) === false;
  }

  private function getCityByPostcode($postcode='', $lang='UA')
  {
    $request = wp_remote_request( 'https://ukrposhta.ua/address-classifier/get_postoffices_by_postindex?pc=' . $postcode );
    $city_name = array();

    if( is_wp_error( $request ) ) {
      return false;
    }

    $body = wp_remote_retrieve_body( $request );
    $body_json = json_encode( $body );

    if( ! empty( $body_json ) ) {
      $xml_obj = simplexml_load_string($body, "SimpleXMLElement", LIBXML_NOCDATA);
      $city_lang0 = 'CITY_' . $lang;
      $city_lang1 = 'PDCITY_' . $lang;
      $city_name[0] = isset( $xml_obj->Entry[0]->$city_lang0 ) ? $xml_obj->Entry[0]->$city_lang0 : '';
      $city_name[1] = isset( $xml_obj->Entry[0]->$city_lang1 ) ? $xml_obj->Entry[0]->$city_lang1 : '';
      return $city_name;
    }
    return false;
  }

}
