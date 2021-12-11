<?php
/**
 * Plugin Name: Ukrposhta
 * Plugin URI: https://morkva.co.ua/woocommerce-plugins/woo-ukrposhta-plahin-dlia-woocommerce/?utm_source=woo-ukrposhta-pro
 * Description:  Генеруйте накладні просто зі сторінки замовлення і зекономте тонну часу на відділенні при відправці.
 * Version: 1.6.11
 * Author: Morkva
 * Text Domain: woo-ukrposhta-pro
 * Domain Path: /languages
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Tested up to: 5.7
 */

if (!defined('ABSPATH'))
{
    exit;
}

if ( ! function_exists( 'morkvaup_fsk' ) ) {
    // Create a helper function for easy SDK access.
    function morkvaup_fsk() {
        global $morkvaup_fsk;

        if ( ! isset( $morkvaup_fsk ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $morkvaup_fsk = fs_dynamic_init( array(
                'id'                  => '3509',
                'slug'                => 'woo-ukrposhta',
                'premium_slug'        => 'nova-poshta-ttn-premium',
                'type'                => 'plugin',
                'public_key'          => 'pk_ca8dbc8f7d6e567355cf59530da68',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'morkvaup_plugin',
                    'first-path'     => 'admin.php?page=morkvaup_plugin',
                    'account'        => false,
                    'contact'        => false,
                    'support'        => false,
                ),
            ) );
        }

        return $morkvaup_fsk;
    }

    // Init Freemius.
    morkvaup_fsk();
    // Signal that SDK was initiated.
    do_action( 'morkvaup_fsk_loaded' );
}

include_once 'autoload.php';


require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugData = get_plugin_data(__FILE__);

if ($plugData['Name'] == 'Ukrposhta') {
    if (file_exists('freemius/freemiusimport.php')) {
        require_once 'freemius/freemiusimport.php';
    }
}


define('MORKVA_UKRPOSHTA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MORKVA_UKRPOSHTA_PLUGIN_PATH', __FILE__);
define('MORKVA_UKRPOSHTA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MORKVA_UKRPOSHTA_VERSION', '1.1');

\deliveryplugin\Ukrposhta\classes\ukrposhtaShipping::instance();

if (!function_exists('morkva_ukrposhta'))
{

    function morkva_ukrposhta()
    {
        return \deliveryplugin\Ukrposhta\classes\ukrposhtaShipping::instance();
    }

}

if (!function_exists('morkva_ukrposhta_import_svg'))
{

    function morkva_ukrposhta_import_svg($image)
    {
        return file_get_contents(MORKVA_UKRPOSHTA_PLUGIN_DIR . '/image/' . $image);
    }

}

if (!function_exists('morkva_ukrposhta_get_option'))
{

    function morkva_ukrposhta_get_option($key)
    {
        return \deliveryplugin\Ukrposhta\classes\OptionsRepository::getOption($key);
    }

}

define('MORKVA_UKRPOSHTA_UP_SHIPPING_NAME', 'ukrposhta_shippping');
define('MORKVA_UKRPOSHTA_UP_SHIPPING_TITLE', 'Доставка службою "Укрпошта"');

// function action_woocommerce_checkout_update_order_reviewupp($array, $int)
// {
//     WC()->cart->calculate_shipping();
//     //return;
// }
//
// add_action('woocommerce_checkout_update_order_review', 'action_woocommerce_checkout_update_order_reviewupp', 10, 2);


function name_of_your_function($posted_data)
{
    global $woocommerce;

    // Parsing posted data on checkout
    $post = array();
    $vars = explode('&', $posted_data);
    foreach ($vars as $k => $value) {
        $v = explode('=', urldecode($value));
        $post[$v[0]] = $v[1];
    }

    // Here we collect chosen payment method
    $payment_method = $post['billing_country'];

    // Run custom code for each specific payment option selected
    if ($payment_method == "UA") {
        // Your code goes here
    }

    // elseif ($payment_method == "bacs") {
    //     // Your code goes here
    // }
    //
    // elseif ($payment_method == "stripe") {
    //     // Your code goes here
    // }
}

add_action('woocommerce_checkout_update_order_review', 'name_of_your_function');


add_filter('woocommerce_checkout_update_order_review', 'clear_wc_shipping_rates_cache');

function clear_wc_shipping_rates_cache()
{
    $packages = WC()
        ->cart
        ->get_shipping_packages();

    foreach ($packages as $key => $value)
    {
        $shipping_session = "shipping_for_package_$key";

        unset(WC()
            ->session
            ->$shipping_session);
    }
}

function mrkv_cart_product_max_size( $default_length ) {
    $items = WC()->cart->get_cart();
    $dimension_unit = get_option( 'woocommerce_dimension_unit' );
    $array_cm = array();
    foreach ( $items as $item => $values ) {
        $_product = wc_get_product( $values['data']->get_id() );
        $array_prod_sizes = array(
            wc_get_dimension( $_product->get_length(), 'cm', $dimension_unit )  ,
            wc_get_dimension( $_product->get_width(), 'cm', $dimension_unit )  ,
            wc_get_dimension( $_product->get_height(), 'cm', $dimension_unit )
        );
        array_push( $array_cm, max( $array_prod_sizes ) );
    }
    $length = max( $default_length, max($array_cm));
    return wc_get_dimension( $length, 'cm', $dimension_unit );
}

function get_price_shipping($country, $citycost, $addr)
{
	$weight_unit = get_option( 'woocommerce_weight_unit' );
	$dimension_unit = get_option( 'woocommerce_dimension_unit' );
	$cartTotal = max( 1, WC()->cart->cart_contents_total );

	if ($country == "UA") {
		$cartWeight = max( 0.5, WC()->cart->cart_contents_weight ); // Якщо у товарів немає ваги, то вага кошика 0.5 кг
	    $cartWeight = ( 'kg' == $weight_unit ) ? $cartWeight * 1000 : $cartWeight;
        $length = intval( ceil( floatval( mrkv_cart_product_max_size( 30 ) ) ) ); // Якщо у товарів немає розмірів, то максимальний розмір товару в кошику 30 см

        $up_shipping_postcode = isset( $_COOKIE['up_shipping_postcode'] ) ? $_COOKIE['up_shipping_postcode'] : '';
	    $params = array(
	        "weight" => $cartWeight,
	        "length" => $length,
	        "addressFrom" => array(
	            "postcode"  => get_option('warehouse')
	        ),
	        "addressTo" => array(
	            "postcode"  => $up_shipping_postcode
	        ),
	        "type"  => get_option('sendtype'),
	        "deliveryType"  => "W2W",
	        "declaredPrice" => $cartTotal
	    );
	    if ( ! class_exists( 'UkrposhtaApi' ) ) {
	        require_once 'admin/partials/api.php';
	    }
	    $bearer = ( null !== get_option( 'production_bearer_ecom' ) ) ? get_option( 'production_bearer_ecom' ) : '';
	    $cptoken = ( null !== get_option( 'production_cp_token' ) ) ? get_option( 'production_cp_token' ) : '';
	    $tbearer = ( null !== get_option( 'production_bearer_status_tracking' ) ) ? get_option( 'production_bearer_status_tracking' ) : '';
	    $ukrposhtaApi = new UkrposhtaApi($bearer, $cptoken, $tbearer);
	    $invoice = $ukrposhtaApi->howcostsua($params);
	    $shipping_price = ( isset( $invoice['deliveryPrice'] ) ) ? $invoice['deliveryPrice'] : 0;
	    return $shipping_price;

		/* //case not requiring api key
		$uptarifs = [
			'0.5' => '25',
			'1' => '26',
			'2' => '28',
			'5' => '36',
			'10' => '48',
			'15' => '55',
			'20' => '65',
			'30' => '85'
		];
		$rate = 0;
		$addw = 0;
		foreach ($uptarifs as $kilo => $price)
		{
			if (($kilo > intval($cartWeight)) && ($addw == 0)){
				$addw = $price;
			}
		}
		$rate += $addw;

		if ($addr == 'checked'){
			$rate += 15;
		}
		else{

		}
		return $rate;*/

	} else { //international case requiring api key

	}
}

function adjust_shipping_rate($rates)
{
    global $woocommerce;
    $index = 0;
    foreach ($rates as $rate)
    {
        if (($rate->get_method_id() == 'ukrposhta_shippping') && (get_option('ukrposhta_calculate_rates')))
        {
            $cost = $rate->cost;
            $post_country = isset( $_POST['country'] ) ? $_POST['country'] : '';
            $country = isset($_POST['billing_country']) ? $_POST['billing_country'] : $post_country;
            $address = isset( $_COOKIE['up_shipping_postcode'] ) ? $_COOKIE['up_shipping_postcode'] : '';
            // $rate->cost = get_price_shipping($country, 'param', $_COOKIE['up_custom_address']);
            $rate->cost = get_price_shipping( $country, 'param', $address );
        }
    }
    return $rates;
}
add_filter('woocommerce_package_rates', 'adjust_shipping_rate', 50, 1);

add_filter('woocommerce_shipping_methods', 'morkva_ukrposhta_add_up_shipping_method');
function morkva_ukrposhta_add_up_shipping_method($methods)
{
    include_once 'classes/ukrPoshtaShipping.php';

    $methods[MORKVA_UKRPOSHTA_UP_SHIPPING_NAME] = 'ukrPoshtaShipping';

    return $methods;
}

new \deliveryplugin\Ukrposhta\classes\ukrPoshtaFrontendInjector();
new \deliveryplugin\Ukrposhta\classes\CheckoutValidator();
new \deliveryplugin\Ukrposhta\classes\OrderCreator();

add_action('woocommerce_admin_order_data_after_shipping_address', function ($order)
{
    $shippingMethod = $order->get_shipping_methods();
    $shippingMethod = reset($shippingMethod);

    if ($shippingMethod && $shippingMethod->get_method_id() === MORKVA_UKRPOSHTA_UP_SHIPPING_NAME)
    {
?>
    <input type="hidden" name="_shipping_state" value="<?=esc_attr($order->get_shipping_state()); ?>" />
<?php
    }
});

add_filter('plugin_action_links_' . plugin_basename(__FILE__) , function ($links)
{
    $settings_link = '<a href="' . home_url('wp-admin/admin.php?page=morkva_ukrposhta_options') . '">Настройки</a>';
    array_unshift($links, $settings_link);

    return $links;
});

////////


require_once ABSPATH . 'wp-admin/includes/plugin.php';
$plugData = get_plugin_data(__FILE__);

define('MUP_PLUGIN_VERSION', $plugData['Version']);
define('MUP_PLUGIN_NAME', $plugData['Name']);
define('MUP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('MUP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MUP_TABLEDB', 'uposhta_invoices');

function activate_morkvaup_plugin()
{
    global $wpdb;

    $table_name = $wpdb->prefix . MUP_TABLEDB;

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
    {
        // if table not exists, create this table in DB
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
       id int(11) AUTO_INCREMENT,
       order_id int(11) NOT NULL,
       order_invoice varchar(255) NOT NULL,
                   invoice_ref varchar(255) NOT NULL,
       PRIMARY KEY(id)
     ) $charset_collate;";
        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    else
    {

    }

    flush_rewrite_rules();
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-morkvaup-plugin-deactivator.php
 */
function deactivate_morkvaup_plugin()
{
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'activate_morkvaup_plugin');
register_deactivation_hook(__FILE__, 'deactivate_morkvaup_plugin');
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-morkvaup-plugin.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_morkvaup_plugin()
{
    $plugin = new MUP_Plugin();
    $plugin->run();
}
run_morkvaup_plugin();
