<?php

namespace morkva\JustinShip\classes;

use morkva\JustinShip\Api\JustinApi;

if ( ! defined('ABSPATH')) {
	exit;
}

class JustinAjaxHandler
{
  private $apiLoader;

	public function __construct()
	{
	  $this->apiLoader = new JustinApiLoader(new JustinApi(''));

		// Activation
    add_action('wp_ajax_woo_justin_np_load_areas', [ $this, 'apiLoadAreas' ]);
    add_action('wp_ajax_nopriv_woo_justin_np_load_areas', [ $this, 'apiLoadAreas' ]);

    add_action('wp_ajax_woo_justin_np_load_cities', [ $this, 'apiLoadCities' ]);
    add_action('wp_ajax_nopriv_woo_justin_np_load_cities', [ $this, 'apiLoadCities' ]);

    add_action('wp_ajax_woo_justin_np_load_warehouses', [ $this, 'apiLoadWarehouses' ]);
    add_action('wp_ajax_nopriv_woo_justin_np_load_warehouses', [ $this, 'apiLoadWarehouses' ]);

	// add_action('wp_ajax_mrkvjs_autocomplete_cities', [ $this, 'mrkvjs_autocomplete_cities' ]); // Не працює. Треба знайти інше місце у плагіні.
	// add_action('wp_ajax_mrkvjs_autocomplete_city_warehouses', [ $this, 'mrkvjs_autocomplete_city_warehouses' ]); // Не працює. Треба знайти інше місце у плагіні.
    // End Activation
	}

	// public function mrkvjs_autocomplete_city_warehouses() { // Не працює. Треба знайти інше місце у плагіні.
	// 	check_ajax_referer('autocompleteSearchNonce', 'security');
	// 	$city_uuid = $_POST['woocommerce_morkvajustin_shipping_method_city'];
	// 	global $wpdb;
	// 	$table = $wpdb->prefix . 'woo_justin_ua_warehouses';
	// 	$sql = "SELECT descr FROM {$table} WHERE city_uuid LIKE '" . $city_uuid . "%'";
	// 	$results = $wpdb->get_col( $sql );
	// 	echo json_encode($results);
	// 	wp_die();
	// }

	// public function mrkvjs_autocomplete_cities() { // Не працює. Треба знайти інше місце у плагіні.
	// 	$keyword = $_POST['term'];
	// 	global $wpdb;
	// 	$table = $wpdb->prefix . 'woo_justin_ua_cities';
	// 	$sql = "SELECT descr FROM {$table} WHERE descr LIKE '".$keyword."%'";
	//
	// 	$results = $wpdb->get_col( $sql );
	// 	echo json_encode($results);
	// 	wp_die();
	// }

	public function apiLoadAreas()
  {
  	$result = $this->apiLoader->loadAreas();

    echo json_encode([
      'result' => $result
    ]);

    wp_die();
  }

  public function apiLoadCities()
  {
  	$result = $this->apiLoader->loadCities();

    echo json_encode([
      'result' => $result
    ]);

    wp_die();
  }

  public function apiLoadWarehouses()
  {
  	$result = $this->apiLoader->loadWarehouses();

    echo json_encode([
      'result' => $result
    ]);

    Activator::setPluginState('activated');

    wp_die();
  }
}
