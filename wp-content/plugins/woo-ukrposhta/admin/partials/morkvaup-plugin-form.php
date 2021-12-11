<?php

session_start();

require("functions.php");

require("api.php");

$path = MUP_PLUGIN_PATH . 'admin/partials/morkvaup-plugin-invoices-page.php';

//встановлюємо змінні станів
$showpage = true;
$message = '';
$requested = false;
$failed = false;
$isInternational = isset($_POST['international']) ? true : false;
$notdimentions = false;

//отримуємо id замовлення
if ((isset($_SERVER['HTTP_REFERER'])) && (isset($output['post']))) {
    $qs = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
    if (!empty($qs)) {//якщо ? get не пусті то отримуємо id  $_GET [post]
        parse_str($qs, $output);
        $order_id =  $output['post'];
    }
}
//встановлено order_id з попередньої умови
if (isset($order_id)) {
    logg('встановлено order_id ');
    $order_data0 = wc_get_order($order_id);
    $order_data = $order_data0->get_data();
    $_SESSION['order_id'] = $order_id;
}
//інакше встановлено order_id з сесії
elseif (isset($_SESSION['order_id'])) {
    logg('else if встановлено  з сесії order_id');
    $order_id = $_SESSION['order_id'];
    $order_data0 = wc_get_order($order_id);
    if ($order_data0) {
        $order_data = $order_data0->get_data();
    }
}
//інакше виводимо в  консоль
else {
    logg('else $showpage=false');
    $showpage = false;
}

// start calculating alternate weight
$varia = null;
if (isset($order_data['line_items'])) {
    $varia = $order_data['line_items'];
}
$alternate_weight = 0;
$dimentions = array();
$d_vol_all = 0;
$weighte = '';

$prod_quantity = 0;
$prod_quantity2 = 0;

$list = '';
$list2 = '';
$descr = '';


if (isset($varia)) {
    foreach ($varia as $item) {
        $data = $item->get_data();

        $quantity = ($data['quantity']);
        $quanti = $quantity;

        $pr_id = $data['product_id'];
        $product = wc_get_product($pr_id);
        if ($product->is_type('variable')) {
            $var_id = $data['variation_id'];
            $variations      = $product->get_available_variations();

            for ($i=0; $i < sizeof($variations) ; $i++) {
                if ($variations[$i]['variation_id'] == $var_id) {

                    while ($quanti > 0) {
                        if (is_numeric($variations[$i]['weight'])) {
                            $alternate_weight += $variations[$i]['weight'];
                        }

                        if (!($variations[$i]['weight'] > 0)) {
                            $weighte = 'Маса вказана не  для всіх товарів в кошику. Радимо уточнити цифри.';
                        }

                        array_push($dimentions, $variations[$i]['dimensions']);
                        if (is_numeric($variations[$i]['dimensions']['length']) && is_numeric($variations[$i]['dimensions']['width']) && is_numeric($variations[$i]['dimensions']['height'])) {
                            $d_vol = $variations[$i]['dimensions']['length'] * $variations[$i]['dimensions']['width'] * $variations[$i]['dimensions']['height'];
                            $d_vol_all += $d_vol;
                        }
                        $quanti--;
                    }
                    //$product = new WC_Product($var_id);

                    $sku = $variations[$i]['sku'];
                    if (!empty($sku)) {
                        $sku = '('.$sku.')';
                    }
                    $name = $product->get_title();

                    $list2  .= $name .$sku. ' x '.$quantity.'шт ;';
                    $list  .= $name .' x '.$quantity.'шт ;';
                    $prod_quantity += 1;
                    $prod_quantity2 += $quantity;
                }
            }
        } else {
            $sku = $product->get_sku();
            if (!empty($sku)) {
                $sku = '('.$sku.')';
            }
            $name = $product->get_title();

            $list2  .= $name .$sku. ' x '.$quantity.'шт ;';

            $list  .= $name . ' x '.$quantity.'шт ;';

            $prod_quantity += 1;
            $prod_quantity2 += $quantity;


            $diment =0;
            if ((is_numeric($product->get_width())) && (is_numeric($product->get_length())) && (is_numeric($product->get_height()))) {
                $diment = $product->get_length() * $product->get_width() * $product->get_height();
                $d_array = array('length'=>$product->get_length(),'width'=> $product->get_width(), 'height'=>$product->get_height() );
                array_push($dimentions, $d_array);
                $d_vol_all += $diment;
            }
            while ($quantity > 0) {
                $weight = $product->get_weight();
                if ($weight > 0) {
                    $alternate_weight += $weight;
                } else {
                    $weighte = 'Маса вказана не  для всіх товарів в кошику. Радимо уточнити цифри.';
                }


                $quantity--;
            }
        }
    }
}


$alternate_vol=0;

$volumemessage = '';
if ((sizeof($dimentions) > 1)) {
    $alternate_vol = $d_vol_all;
    $volumemessage = 'УВАГА! В відправленні кілька товарів. Ми порахували арифметичний сумарний об\'єм посилки, враховуючи мета-дані товарів. Ви можете змінити об\'єм зараз вручну, щоб бути більш точним.' ;
} else {
    if (isset($variations)) {
        if (is_numeric($variations[0]['dimensions']['length']) &&  is_numeric($variations[0]['dimensions']['width']) &&  is_numeric($variations[0]['dimensions']['height'])) {
            $alternate_vol = $variations[0]['dimensions']['length'] * $variations[0]['dimensions']['width'] * $variations[0]['dimensions']['height'];
            $volumemessage = '';
        }
    }
}
$alternate_vol = $alternate_vol / 1000000;

//setting up credentials
$invoice = array();
$bearer = get_option('production_bearer_ecom');
$cptoken = get_option('production_cp_token');
$tbearer = get_option('production_bearer_status_tracking');
logg('setted up $bearer $cptoken $tbearer');

$ukrposhtaApi = new UkrposhtaApi($bearer, $cptoken, $tbearer);
logg('created  istance of $ukrposhtaApi');

$address1_id = isset( $address1['id'] ) ? $address1['id'] : null;
$address2_id = isset( $address2['id'] ) ? $address2['id'] : null;
if ( ! $isInternational ) {
	// Create Recipient address: $address1.
	if ( isset( $_POST['index1'] ) ) {
		$address1 = $ukrposhtaApi->modelAdressPost( array( "postcode" => $_POST['index1']  ) );
		if ( isset( $address1['id'] ) ) {
			$address1_id = $address1['id'];
		} else {
			$failed = true;
			$message .= 'Помилка в поштовому індексі Відправника. ';
	        $message .= $address1['message'] . '. ';
	    }
	} else {
		$message .= 'Проблема з індексом Відправника2. ';
	}
	// Create Recipient address: $address2.
	if ( isset( $_POST['index2'] ) ) {
		$address2 = $ukrposhtaApi->modelAdressPost( array( "postcode" => $_POST['index2'] ) );
	    if ( isset( $address2['id'] ) ) {
	    	$address2_id = $address2['id'];
	    } else {
	        $failed = true;
	        $message .= 'Помилка в поштовому індексі Одержувача. ';
	        $message .= $address2['message'] . '. ';
	    }
	} else {
		$message .= 'Проблема з індексом Одержувача2. ';
	}
}
// Create Sender client: $client1.
$client1Type = isset( $_POST['up_sender_type'] ) ? sanitize_text_field($_POST['up_sender_type']) : null;
$name11 = isset( $_POST['sender_first_name'] ) ? $_POST['sender_first_name'] : null;
$name12 = isset($_POST['sender_last_name']) ? $_POST['sender_last_name'] : null;
$phone1 = isset( $_POST['phone1'] ) ? $_POST['phone1'] : null;
$companySenderName = isset( $_POST['up_company_sender_name'] ) ? $_POST['up_company_sender_name'] : null;
$companySenderEdrpou = isset($_POST['up_company_sender_edrpou']) ? $_POST['up_company_sender_edrpou'] : null;
$selfEmployedPersonTIN = isset($_POST['up_sep_tin']) ? $_POST['up_sep_tin'] : null;
if ( ! $failed ) {
    $client1 = $ukrposhtaApi->modelClientsPost( array(
	    "type"			=> $client1Type,
	    "name"			=> $companySenderName,
	    "edrpou"		=> $companySenderEdrpou,
        "tin"           => $selfEmployedPersonTIN,
	    "firstName"		=> $name11,
	    "lastName"		=> $name12,
	    "addressId"		=> $address1_id,
	    "phoneNumber"	=> $phone1
    ));
}
// Create Recipient client: $client2.
$name21 = isset( $_POST['rec_first_name'] ) ? $_POST['rec_first_name'] : null;
if ( strpos ( $name21, "'" ) !== false ) {
	$name21 = str_replace( "\\", "", $name21 );
}
$name22 = isset( $_POST['rec_last_name'] ) ? $_POST['rec_last_name'] : null;
$phone2 = isset( $_POST['phone2'] ) ? $_POST['phone2'] : null;
if ( ! $failed ) {
    $client2 = $ukrposhtaApi->modelClientsPost( array(
	    "type"			=> 'INDIVIDUAL',
	    // "name"			=> null,
	    "firstName"		=> $name21,
	    "lastName"		=> $name22,
	    "addressId"		=> $address2_id,
	    "phoneNumber"	=> $phone2
    ));
}

// Create invoice for Ukraine
$order_data_ua = (isset( $_SESSION['order_id'] ) ) ? wc_get_order($_SESSION['order_id']) : $order_data['id'];
$dimension_unit = get_option( 'woocommerce_dimension_unit' );
$array_cm = array();

// Get and Loop Over Order Items
if ( isset( $order_data_ua ) ) {
    $order_ua_items = ( null !== $order_data_ua->get_items() ) ? $order_data_ua->get_items() : '';

    if ( ! empty( $order_ua_items ) ) {
        foreach ( $order_ua_items as $item_id => $item ) {
            $_product = $item->get_product();
            $array_prod_sizes = array(
                wc_get_dimension( $_product->get_length(), 'cm', $dimension_unit )  ,
                wc_get_dimension( $_product->get_width(), 'cm', $dimension_unit )  ,
                wc_get_dimension( $_product->get_height(), 'cm', $dimension_unit )
            );
            array_push( $array_cm, max( $array_prod_sizes ) );
        }
    }
} else { wp_die('<h3>Для створення накладної перейдіть на <a href="edit.php?post_type=shop_order">сторінку замовлення</a></h3>'); }

if ( 0 == max( $array_cm ) ) {
    $notdimentions = true;
}
$length_max_ua = max( 30, max($array_cm)); // Якщо у товарів немає розмірів, то максимальний розмір товару в кошику 30 см
$length_ua = wc_get_dimension( $length_max_ua, 'cm', $dimension_unit );

$ua_shipping_method = $order_data_ua->get_shipping_methods();
$shipping_method = @array_shift($ua_shipping_method);
$shipping_method_id = $shipping_method['method_id'];
$deliveryType = ( 'ukrposhta_shippping' == $shipping_method_id ) ? 'W2W' : 'W2D'; // W2W: Відділення - Відділення, W2D: Відділення - Двері

$shipmentType = isset( $_POST['sendtype'] ) ? $_POST['sendtype'] : null; // EXPRESS, STANDART

$client1uuid =  isset( $client1['uuid'] ) ? $client1['uuid'] : null;
$client2uuid =  isset( $client2['uuid'] ) ? $client2['uuid'] : null;
$paidByRecipient = isset( $_POST['paidByRecipient'] ) ? $_POST['paidByRecipient'] : null;
$description = isset( $_POST['up_invoice_description'] ) ? $_POST['up_invoice_description'] : null;
$onFailReceiveType = isset( $_POST['onFailReceiveType'] ) ? $_POST['onFailReceiveType'] : null;
$invoicePlaces = isset( $_POST['invoice_places'] ) ? $_POST['invoice_places'] : null;
$invoiceCargoMass = isset( $_POST['invoice_cargo_mass'] ) ? $_POST['invoice_cargo_mass'] : null;
$invoiceVolume = isset( $_POST['invoice_volume'] ) ? intval( ceil( floatval( $_POST['invoice_volume'] ) ) ) : 10;
$declaredPrice = isset( $_POST['declaredPrice'] ) ? intval($_POST['declaredPrice']) : null;
$invoice = $ukrposhtaApi->modelShipmentsPost( array(
	"sender" 			=> array( "uuid" => $client1uuid ),
	"recipient" 		=> array( "uuid" => $client2uuid ),
	"type" 				=> $shipmentType,
	"checkOnDelivery" 	=> true,
	"deliveryType"		=> $deliveryType,
	"paidByRecipient"	=> $paidByRecipient,
	// "nonCashPayment"	=> false,
	"description" 		=> $description,
	"onFailReceiveType" => $onFailReceiveType,
	"postPay" 			=> $invoicePlaces,
	"parcels"			=> array( array(
		"weight"			=> woo_setting_weihgt_unit() * $invoiceCargoMass,
		"length"			=> $invoiceVolume,
		"declaredPrice" 	=> $declaredPrice
   ))
));

// створення адрес #2 якщо встановлені індекси. і відправлення міжнародне але не встановлений id адреси відправника
if (isset($_POST['index1']) && isset($_POST['index2']) && isset($_POST['international']) /*&& !isset($_POST['address1id'])*/) {
    logg('002 - встановлені поштові індекси і відправлення міжнародне, але не встановлений id адреси відправника');
    $address1 = $ukrposhtaApi->modelAdressPost(array(
      "country"=>"UA",
      "city"=>$_POST['city0'], "region"=> $_POST['city0'], "postcode"=>$_POST['index1'], "street"=> $_POST['street'], "houseNumber"=>$_POST['housenumber']));
    if (isset($address1['id'])) {
        $address1_id = $address1['id'];
    } else {
        $failed=true;
        $message .= 'Проблема з індексом відправника. ';
        $message .= 'not set($address1[id]. ';
    }
    $address2 = $ukrposhtaApi->modelAdressPost(array(
        "country"=>$_POST['country_rec'],
        "city"=>$_POST['rec_city_name'],
        "postcode"=>$_POST['postcode2'],
        "foreignStreetHouseApartment" => $_POST['foreignStreetHouseApartment']
    ));
    if (isset($_POST['postcode2'])) {
        $address2_id = $address1['id'];
    }
    if (!isset($address2['id'])) {
        $failed = true;
        $message .= 'Проблема з адресою отримувача міжнародного відправлення. ';
        $message .= ' ' . $address2['message'] . '. ';
    } else {
        $address2_id = $address2['id'];
    }
}
//кінець створення адрес #2

//створення клієнтів
if (isset($_POST['phone1']) && isset($_POST['phone2']) && !$failed && !isset($_POST['international'])) {
    logg('005 - встановлені телефони 1 і 2 && !failed && !international');
} elseif ($failed) {//failed
    // $message .= 'Проблеми з правильністю телефонів';
    $message .= '<br>Інші невідомі помилки. ';
    logg('006 - Проблеми з правильністю телефонів');
}
//створення відправлення
if ((isset($client1['uuid']) && isset($client2['uuid'])) && !isset($_POST['international'])) {
    logg('009 - instance $ukrposhtaApi->modelShipmentsPost');
    if (isset($invoice['uuid'])) {
        $ref = $invoice['uuid'];
        $barcode = $invoice['barcode'];
        global $wpdb;
        $query = 'INSERT INTO '.$wpdb->prefix.'uposhta_invoices (order_id, order_invoice, invoice_ref) VALUES ("'.$order_data["id"].'", "'.$barcode.'", "'.$ref.'");';
        //echo $query;
        $requested = true;
        $wpdb->query($query);

        $order = wc_get_order($order_data["id"]);

        $meta_key = 'ukrposhta_ttn';
        update_post_meta($order_id, $meta_key, $barcode);

        $note = "Номер відправлення: " . $barcode .". Укрпошта.";
        $order->add_order_note($note);
        $order->save();


        logg('0010 - Відправлення створено і записано до бд');
    } else {
        ('0011 - Помилка');
    }
}
//кінець створення відправлення

else {
    logg("Не задоволено жодної умови для міжнародного відправлення.");
    $failed = true;
    if (isset($client1['message'])) {
        $message .= $client1['message'].'. ';
    }
    if (isset($client2['message'])) {
        $message .= $client2['message'].'. ';
    }
}
echo '<br>';

if (isset($order_data["billing"]["first_name"])) {
    $shipping_first_name = $order_data["billing"]["first_name"];
} elseif (isset($order_data["shipping"]["first_name"])) {
    $shipping_first_name = $order_data["shipping"]["first_name"];
} else {
    $shipping_first_name = "";
}

if (isset($order_data["billing"]["last_name"])) {
    $shipping_last_name = $order_data["billing"]["last_name"];
} elseif (isset($order_data["shipping"]["last_name"])) {
    $shipping_last_name = $order_data["shipping"]["last_name"];
} else {
    $shipping_last_name = "";
}

if (isset($order_data["billing"]["address_2"])) {
    $shipping_address = $order_data["billing"]["address_2"];
    $shipping_address = explode(" ", $shipping_address);
} elseif (isset($order_data["shipping"]["address_2"])) {
    $shipping_address = $order_data["shipping"]["address_2"];
    $shipping_address = explode(" ", $shipping_address);
} else {
    $shipping_address[0] = "";
    $shipping_address[1] = "";
}
/* OTHER GETTING DATA FUNCTIONS */

if (isset($order_data["billing"]["city"])) {
    $shipping_city = $order_data["billing"]["city"];
} elseif (isset($order_data["shipping"]["city"])) {
    $shipping_city = $order_data["shipping"]["city"];
} else {
    $shipping_city = "";
}

if (isset($order_data["billing"]["state"])) {
    $shipping_state = $order_data["billing"]["state"];
} elseif (isset($order_data["shipping"]["state"])) {
    $shipping_state = $order_data["shipping"]["state"];
} else {
    $shipping_state = "";
}

if (isset($order_data["billing"]["phone"])) {
    $shipping_phone = $order_data["billing"]["phone"];
} elseif (isset($order_data["shipping"]["phone"])) {
    $shipping_phone = $order_data["shipping"]["phone"];
} else {
    $shipping_phone = "";
}

function startsWith($string, $startString)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}
?>

<script src="<?php echo MUP_PLUGIN_URL . 'admin/js/script.js?ver='.MUP_PLUGIN_VERSION.' '; ?>"></script>
<link rel="stylesheet" href="<?php echo MUP_PLUGIN_URL . 'admin/css/style.css?ver='.MUP_PLUGIN_VERSION.' '; ?>"/>


<?php 	mup_display_nav(); ?>

      <style>.form-table th {
          font-size: 13px;
      }</style>
<div class="container">
    <?php $order_number = isset( $order_data['id'] ) ? $order_data['id'] : 0; ?>
	<h1 style="font-size:23px;font-weight:400;line-height:1.3;"><?php echo 'Нове відправлення Укрпошти №' . $order_number; ?></h1>

  <?php if ($showpage) { ?>

  <form class="form-invoice" action="admin.php?page=morkvaup_invoice" method="post" name="invoice">
    <?php  if ($requested) { ?>
    <div id="messagebox" class="messagebox_show updated" data="186" style="height:0px;padding:0px">
				<div class="sucsess-naklandna">
                    <h3>Відправлення <?php echo $barcode; ?> успішно створене!</h3>
					<p>
						Тип відправлення: <?php echo $invoice['type']; ?><br>
						<?php if ( isset($_POST['international']) ) {
							echo 'Тип паковання: ' . $senduptype . '<br>';
						} ?>
            Відправник: <?php echo $invoice['sender']['name']; ?></br>Адреса відправлення: <?php echo $invoice['sender']['addresses'][0]['address']['postcode']; ?> <?php echo $invoice['sender']['addresses'][0]['address']['detailedInfo']; ?> <br>
            Одержувач: <?php echo $invoice['recipient']['name']; ?></br>Адреса отримання: <?php echo $invoice['recipient']['addresses'][0]['address']['postcode']; ?>  <?php echo $invoice['recipient']['addresses'][0]['address']['detailedInfo']; ?><br>

					</p>
				</div>
		</div>
        <?php if ( $notdimentions ) : ?>
        <div class="notice notice-warning is-dismissible" style="margin:0 -5px 0 0;">
            <?php if ( $isInternational ): ?>
                <p>Встановлені розміри Відправлення за замовчуванням: 20х10х5 см.</p>
            <?php else : ?>
                <p>Встановлені розміри Відправлення за замовчуванням: 30х20х10 см.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
  <?php }
  elseif ($failed && isset($message) && isset($_POST['sender_first_name']) || isset($_POST['up_company_sender_name']) || isset($_POST['up_company_sender_edrpou']) ) { ?>
    <div id="messagebox" class="messagebox_show error" data="110" style="height: 0px;padding:0px;">
      <div class="card text-white bg-danger">
        <h3>Введені невірні дані. Перевірте встановлену вагу.</h3>
        <p><?php echo $message . $ukrposhtaApi->httpCode401 . $ukrposhtaApi->httpCode403 . $ukrposhtaApi->httpCode404; ?></p>

        <div class="clr"></div>
      </div>
    </div>
  <?php } ?>

    <div class="alink">
      <?php
      if (!empty($order_data["id"])) {
          echo '<a class="btn" href="/wp-admin/post.php?post=' . $order_data["id"] . '&action=edit">Повернутись до замовлення</a>';
          echo '';
      }
      ?>
    <a href="edit.php?post_type=shop_order">Повернутись до замовлень</a>
   </div>


<div class="tablecontainer">
  <table class="form-table full-width-input i4x"  id="i4x">
    <tbody>
      <tr class="international-inner" t=user1>
        <th colspan=2>
          <h3 class="formblock_title">Відправник (заповнювати латиницею)</h2>
          <div id="errors"></div>
        </th>
      </tr>
      <tr class="nv-if-international" t=user1>
        <th colspan=2>
          <h3 class="formblock_title">Відправник</h3>
            <input type="hidden" name="up_sender_type" id="up_sender_type"
            	value="<?php $up_sender_type = esc_attr( get_option( 'up_sender_type' ) ); echo $up_sender_type; ?>">
          <div id="errors"></div>
        </th>
      </tr>
      <?php if ( 'COMPANY' == $up_sender_type ) : ?>
        <tr t=user1>
	        <th scope="row">
	          	<label for="up_company_sender_name">Назва компанії *</label>
	        </th>
	        <td>
	          	<input type="text" id="up_company_sender_name" name="up_company_sender_name" class="input sender_name"
	          		value="<?php echo esc_attr( get_option('up_company_name') ); ?>" placeholder="Не більше 60 символів" />
	        </td>
      	</tr>
      	<tr t=user1>
	        <th scope="row">
	          	<label for="up_company_sender_edrpou">ЄДРПОУ *</label>
	        </th>
	        <td>
	          	<input type="text" id="up_company_sender_edrpou" name="up_company_sender_edrpou" class="input sender_name"  value="<?php echo  esc_attr(get_option('edrpou')); ?>" />
	        </td>
      	</tr>
      <?php endif; ?>
      <tr  class="international-inner">
        <th scope="row">
            <label for="latinname">Повне Ім`я *</label>
        </th>
        <td>
            <input type="text" name="latinname0" id="latinname0"  value="<?php echo esc_attr(get_option('nameslatin')); ?>">
          </td>
      </tr>
      <?php if ( 'INDIVIDUAL' == $up_sender_type ) : ?>
		<tr t=user1>
			<th scope="row">
			  	<label for="sender_first_name">Прізвище *</label>
			</th>
			<td>
			  	<input type="text" id="sender_first_name" name="sender_first_name" class="input sender_name"  value="<?php echo  esc_attr(get_option('names1')); ?>" required />
			</td>
		</tr>
		<tr t=user1>
			<th scope="row">
			  	<label for="sender_first_name">Імя *</label>
			</th>
			<td>
			  	<input type="text" id="sender_last_name" name="sender_last_name" class="input sender_name" value="<?php  echo esc_attr(get_option('names2')); ?>" required />
			</td>
		</tr>
  	  <?php endif; ?>
      <?php if ( 'PRIVATE_ENTREPRENEUR' == $up_sender_type ) : ?>
        <tr t=user1>
            <th scope="row">
                <label for="up_company_sender_name">Фізична особа-підприємець (ФОП) *</label>
            </th>
            <td>
                <input type="text" id="up_company_sender_name" name="up_company_sender_name" class="input sender_name"
                    value="<?php echo esc_attr( get_option('up_company_name') ); ?>" placeholder="Не більше 60 символів" />
            </td>
        </tr>
        <tr t=user1>
            <th scope="row">
                <label for="up_company_sender_edrpou">Індивідуальний податковий номер (ІПН) *</label>
            </th>
            <td>
                <input type="text" id="up_sep_tin" name="up_sep_tin" class="input sender_name"  value="<?php echo  esc_attr(get_option('up_tin')); ?>" />
            </td>
        </tr>
        <tr t=user1>
            <th scope="row">
                <label for="sender_first_name">Прізвище *</label>
            </th>
            <td>
                <input type="text" id="sender_first_name" name="sender_first_name" class="input sender_name"  value="<?php echo  esc_attr(get_option('names1')); ?>" required />
            </td>
        </tr>
        <tr t=user1>
            <th scope="row">
                <label for="sender_first_name">Імя *</label>
            </th>
            <td>
                <input type="text" id="sender_last_name" name="sender_last_name" class="input sender_name" value="<?php  echo esc_attr(get_option('names2')); ?>" required />
            </td>
        </tr>
      <?php endif; ?>
      <tr  class="international-inner">
        <th scope="row">
            <label for="city0">Населений пункт</label>
        </th>
        <td>
            <input id="city0" type="text" name="city0" value="<?php echo esc_attr(get_option('citylatin')); ?>" />
          </td>
      </tr>
      <tr  class="international-inner">
        <th scope="row">
            <label for="street_sender">вулиця</label>
        </th>
        <td>
            <input id="street_sender" type="text" name="street" value="<?php  echo esc_attr(get_option('streetlatin')); ?>">
          </td>
      </tr>

      <tr  class="international-inner">
        <th scope="row">
            <label for="house_sender">Номер будинку</label>
        </th>
        <td>
            <input id="house_sender" type="text" name="housenumber" value="<?php echo esc_attr(get_option('numlatin')); ?>">
          </td>
      </tr>
      <tr t=address1>
       <th scope="row">
         <label for="index1">Індекс відділення подачі відправлення *</label>
       </th>
       <td>
          <input id="index1" type="text"  value="<?php  echo esc_attr(get_option('woocommerce_store_postcode')); ?>"  name="index1" required />
        </td>
      </tr>
      <!--tr t=address1>
       <th scope="row">
         <label for="sender_namecity">Адреса</label>
       </th>
       <td>
          <input id="invoice_sender_address" type="text" value=""  name="invoice_sender_address" />
        </td>
      </tr-->

      <tr t=user1>
       <th scope="row">
         <label for="phone1">Телефон *</label>
       </th>
       <td>
          <input id="phone1" type="text"  value="<?php  echo esc_attr(get_option('phone')); ?>"  name="phone1" required />
        </td>
      </tr>
      <!--tr>
        <th scope="row">
          <label for="invoice_description">Опис відправлення</label>
        </th>
        <td class="pb7">
          <textarea  type="text" id="invoice_description" name="invoice_description" class="input" minlength="1" required><?php //echo get_option('invoice_description'); ?></textarea>
          <p id="error_dec"></p>
        </td>
      </tr-->
    </tbody>
  </table>

  <table id=i5x class="form-table full-width-input i5x">
    <tbody>
      <tr class="nv-if-international">
        <th colspan=2>
          <h3 class="formblock_title">Одержувач</h2>
        </th>
      </tr>
      <tr class="international-inner">
        <th colspan=2>
          <h3 class="formblock_title">Одержувач (заповнювати латиницею)</h2>
        </th>
      </tr>
      <tr  class="international-inner">
        <th scope="row">
            <label for="country_rec">Код країни одержувача *</label>
        </th>
        <td>

          <input id="country_rec" type="text" id="country_rec" name="country_rec" list="country" value="<?php echo $order_data['billing']['country']; ?>"  />
            <datalist id=country name=countries>за
              <?php require 'countries.php'; ?>
            </select>

          </td>
      </tr>

      <tr  class="international-inner">
        <th scope="row">
            <label for="rec_city_name">Населений пункт</label>
        </th>
        <td>
            <input type="text" name="rec_city_name" id="rec_city_name" value="<?php echo $order_data['billing']['city']; ?>">
          </td>
      </tr>

      <tr  class="international-inner">
       <th scope="row">
           <label for="postcode_rec">Індекс</label>
       </th>
       <td>
         <input id="postcode_rec" type="text" name="postcode2" value="<?php echo $order_data['billing']['postcode'] ; ?>">
         </td>
      </tr>

       <tr  class="international-inner">
        <th scope="row">
            <label for="address_rec">Детальна адреса</label>
        </th>
        <td>
            <textarea id="address_rec" name="foreignStreetHouseApartment"><?php echo $order_data['billing']['address_1']; echo " "; echo $order_data['billing']['address_2']; if (!empty($order_data['billing']['pofstcode'])) {
          echo ", ".$order_data['billing']['postcode'];
      }?></textarea>
          </td>
      </tr>
      <tr>
         <!--td colspan="2">
           <div class="flex-space-around">
             <div  class="flexd50">
               <input type="radio" id="contactChoice1"
                name="contact" value="email">
              <label for="contactChoice1">Юридична особа</label>
            </div>
            <div class="flexd50">
              <input type="radio" id="contactChoice2"
               name="contact" value="phone">
              <label for="contactChoice2">Фізична особа</label>
            </div>
          </div>
          <div>
         </td-->
       </tr>
       <tr  class="international-inner">
        <th scope="row">
            <label for="latinname">Ім`я *</label>
        </th>
        <td>
            <input type="text" name="latinname" id=latinname  value="<?php echo $shipping_first_name." ".$shipping_last_name; ?>">
          </td>
      </tr>
       <tr class="nv-if-international">
          <th scope="row">
            <label for="rec_first_name">Прізвище *</label>
          </th>
          <td>
            <input type="text" name="rec_first_name" id="rec_first_name" class="input recipient_name" value="<?php echo esc_html($shipping_last_name); ?>" required />
          </td>
        </tr>
        <tr class="nv-if-international">
           <th scope="row">
             <label for="rec_last_name">Ім'я *</label>
           </th>
           <td>
             <input type="text" name="rec_last_name" id="rec_last_name" class="input recipient_name" value="<?php echo esc_html($shipping_first_name); ?>" required />
           </td>
         </tr>
         <?php  $postcode = '';
              if (isset($order_data['billing']['postcode']) && ($order_data['billing']['postcode'] != '')) {
                  $postcode = $order_data['billing']['postcode'];
              }
              if (isset($order_data['shipping']['postcode']) && ($order_data['shipping']['postcode'] != '')) {
                  $postcode = $order_data['shipping']['postcode'];
              }

              ?>
          <tr class="nv-if-international">
             <th scope="row">
               <label for="index2">Поштовий індекс *</label>
             </th>
             <td>
              <input id="index2" type="text" name="index2" class="input recipient_region" value="<?php echo esc_html($postcode); ?>" required />
           </tr>
           <tr t=user1>
            <th scope="row">
              <label for="phone2">Телефон *</label>
            </th>
            <td>
              <?php

              $shipping_phone.= ' ';

              if (startsWith($shipping_phone, '38')) {
                  $shipping_phone = substr($shipping_phone, 2);
                  echo '<script>console.log("1");</script>';
              }
              if (startsWith($shipping_phone, '+38')) {
                  $shipping_phone = substr($shipping_phone, 3);
                  echo '<script>console.log("11");</script>';
              }
              if (startsWith($shipping_phone, '8')) {
                  $shipping_phone = substr($shipping_phone, 1);
                  echo '<script>console.log("2");</script>';
              }

              ?>
               <input id="phone2" type="text" value="<?php echo '' . $shipping_phone; ?>"  name="phone2" required />
             </td>
           </tr>
        </tbody>
      </table>
    </div>
    <div class="tablecontainer">
      <table id="i6x" class="form-table full-width-input i6x">
        <tbody>
      <tr>
       <th colspan=2>
         <h3 class="formblock_title">Параметри відправлення</h3>
     </th>
     </tr>

     <tr class="international-inner">
       <th scope="row">
         Коротко опишіть вміст посилки
       </th>
       <td>
       <input type=text name=parcelname placeholder="наприклад 'Shirt'"><p>Обов'язково для міднародного відправлення. Якщо не буде значення впишеться "Shirt"'</p>  </td>
     </tr>

      <tr>
         <th scope="row">
           <label for="invoice_payer">Платник</label>
         </th>
         <td>
           <select id="invoice_payer" name="paidByRecipient">
             <option value="true">Отримувач</option>
             <option value="false">Відправник</option>
           </select>
         </td>
       </tr>
       <?php

        if (file_exists($path)) {      ?>
        <tr>
         <th scope="row">
           <label for="invoice_payer">Міжнародне відправлення</label>
         </th>
         <td>
          <input type=checkbox name="international" id="up-international" value="1"  <?php echo ($isInternational == 1 ? 'checked' : ''); ?> />
         </td>
       </tr>
       <?php }
       // $invoice_weight = get_option( 'invoice_weight' );?>
       <?php // if ( !empty( $invoice_weight ) ):?>

          <tr>
             <th scope="row">
               <label class="light" for="invoice_cargo_mass" >Вага, <?php echo woo_name_weihgt_unit_translate(); ?></label>
             </th>
             <?php

             $invoice_addweight = intval(get_option('invoice_addweight'));

             $Weight_object = null ;

             if (isset($order_data['meta_data'][1])) {
                 $Weight_object = ($order_data['meta_data'][1]);
             }

             $weight_value = null;

             if (isset($Weight_object)) {
                 $weight_value  =  $Weight_object -> get_data();
             }

             $order_weight = 0;

             if (isset($weight_value['value']['data']['Weight'])) {
                 $order_weight = $weight_value['value']['data']['Weight'];
             } else {
                 $order_weight = $alternate_weight;
             }

             $all_weight = $order_weight + $invoice_addweight;

             ?>
             <td>
               <input type="text" name="invoice_cargo_mass" required id="invoice_cargo_mass" value="<?php echo $all_weight; ?>"  />
             </td>
           </tr>
           <tr>
            <td colspan=2>
              <p>
                <?php if ($order_weight > 0) {
                 echo '<span> Вага замовлення: ' . $order_weight. ' ' . woo_name_weihgt_unit_translate() . '.<br></span>';
             } else {
                    echo '<span> Вагу замовлення не пораховано тому що вага товарів відсутня.<br></span>';
                }

                if ($invoice_addweight > 0) {
                    echo '<span> Вага упаковки: ' . $invoice_addweight . ' ' . woo_name_weihgt_unit_translate() . '.<br></span>';
                } else {
                    echo '<span> Вагу упаковки не пораховано тому що дані про вагу упаковки відсутні. </span>';
                }


                ?>
               </p>
               <p class="light"><!-- Якщо залишити порожнім, буде використано мінімальне значення 0.5. --> <?php echo $weighte; ?></p>
            </td>
           </tr>


           <!--tr>
              <td colspan=2 class="flexd" >
                <label class="inl" for="invoice_x">Ширина (см):
                <input  type="text" id="invoice_x" name="invoice_x" /></label>
                <label class="inl" for="invoice_z">Висота (см):
                <input  type="text" id="invoice_z" name="invoice_z" /></label>
                <label class="inl" for="invoice_y">Довжина (см):
                <input  type="text"  id="invoice_y" name="invoice_y" /></label>
              </td>
            </tr-->
        <?php // endif;?>
         <tr>
           <th scope="row">
             <label class="light" for="invoice_volumei">Найбільша сторона, см</label>
           </th>
           <td>
             <input type="text" id="invoice_volumei" name="invoice_volume" value="<?php echo (! empty( $length_ua ) ? $length_ua : $length_inter ); ?>" required />
           </td>
         </tr>
            <?php if ( 'PARCEL' == get_option( 'senduptype' ) && $isInternational || ! $isInternational ) : ?>
            <tr>
                <th scope="row">
                    <label for="invoice_priceid">Заявлена цінність</label>
                </th>
                <td>
                    <input id="invoice_priceid" type="text" name="declaredPrice" value="<?php echo esc_html( $order_data['total'] - $order_data['shipping_total'] ); ?>" />
                </td>
            </tr>
            <?php endif; ?>
             <tr>
                <th scope="row">
                  <label for="invoice_placesi">Післяплата, грн</label>
                </th>
                <td style="padding-bottom: 0;">
                  <input type="text" id="invoice_placesi" name="invoice_places" value="<?php

                  $mess1 = '';

                  if (($order_data['payment_method'] == 'cod')) {
                      echo esc_html( $order_data['total'] - $order_data['shipping_total'] );
                      $mess1 = 'При замовленні було обрано оплату при отриманні,  в післяплату автоматично вписана сума замовлення.';
                  } else {
                         echo '0';
                         $mess1 = 'При замовленні не було обрано оплату при отриманні, тому в післяплату автоматично вписано 0.';
                     }
                      ?>
                  " required/>

                </td>
              </tr>
              <tr><td colspan=2><p style="font-size:90%"><?php echo $mess1; ?></p></td></tr>
              <tr>
                <th scope="row">
                  <label for="up_invoice_description">Додаткова інформація</label>
                </th>
                <td class="pb7">
                  <?php

                  $path = MUP_PLUGIN_PATH . '/admin/partials/morkvaup-plugin-invoices-page.php';
                  if (file_exists($path)) {
                      $id = $order_data['id'];
                      $descriptionarea = get_option('up_invoice_description');
                      $descriptionarea = str_replace("[list_qa]", $list2, $descriptionarea);
                      $descriptionarea = str_replace("[list]", $list, $descriptionarea);
                      $descriptionarea = str_replace("[q]", $prod_quantity, $descriptionarea);
                      $descriptionarea = str_replace("[qa]", $prod_quantity2, $descriptionarea);
                      $descriptionarea = str_replace("[p]", $order_data['total'], $descriptionarea);
                  } else {
                      $descriptionarea = '';
                  }

                  ?>
                  <textarea  type="text" id="up_invoice_description" name="up_invoice_description" class="input" minlength="1" placeholder="Не більше 40 символів" required><?php echo esc_textarea($descriptionarea); ?></textarea>
                  <p id="error_dec"></p>
                </td>
              </tr>
              <tr>
                <th colspan=2  scope="row">У разі не вручення:</th>
              </tr>
              <tr>
                <td colspan=2>
                  <div class="">
                    <div  class="onfail ">
                      <input type="radio" id="dqq"
                       name="onFailReceiveType" value="RETURN">
                     <label for="dqq">повернути відправнику через 14 календарних днів.</label>
                   </div>
                   <div class="onfail ">
                     <input checked type="radio" id="dqq2"
                      name="onFailReceiveType" value="RETURN_AFTER_7_DAYS">
                     <label for="dqq2">повернути відправлення після закінчення строку безкоштовного зберігання (5 робочих днів).</label>
                   </div>
                   <div class="onfail">
                     <input type="radio" id="dqq3"
                      name="onFailReceiveType" value="PROCESS_AS_REFUSAL">
                     <label for="dqq3">знищити відправлення</label>
                   </div>
                </td>
              </tr>
            </tbody>
          </table>
  <table class="form-table full-width-input i7x">
    <tbody>
        <tr>
          <td>
            <input type="hidden" name="sendtype" value="<?php  echo esc_attr(get_option('sendtype')); ?>" />
            <input type="hidden" name="sendwtype" value="<?php  echo esc_attr(get_option('sendwtype')); ?>" />
            <input type="submit" value="Створити" class="checkforminputs button button-primary" id="submit"/>
          </td>
        </tr>
      </tbody>
    </table>
</div>

<?php include 'card.php' ; ?>

  </div>
  * - обов'язкове для заповнення поле
</form>
<?php } ?>
<?php if (!$showpage) {
                      echo '<h3>Для створення накладної перейдіть на <a href="edit.php?post_type=shop_order">сторінку замовлення</a></h3>';
                  } ?>
</div>



<?php
if (false) {
                      $invoice = new MUP_Plugin_Invoice();
                      $invoiceController = new MUP_Plugin_Invoice_Controller();

                      $invoice->setPosts();

                      $owner_address = get_option('warehouse');
                      $owner_address = explode(" ", $owner_address);

                      if (empty($owner_address[0] or empty($owner_address[1]))) {
                          $owner_address[0] = "";
                          $owner_address[1] = "";
                          exit('Поле адреса віділення в налаштуваннях пусте, заповніть його, будь ласка');
                      }

                      $invoice->sender_street = $owner_address[0];
                      //$invoice->sender_building = $owner_address[1];
                      $invoice->order_price = $order_data["total"];

                      $invoiceController->isEmpty();

                      $bad_symbols = array( '+', '-', '(', ')', ' ' );

                      $invoice->sender_phone = str_replace($bad_symbols, '', $invoice->sender_phone);

                      $invoice->cargo_weight = str_replace(".", ",", $invoice->cargo_weight);

                      $invoice->register();
                      $invoice->getCitySender();
                      $invoice->getSender();
                      $invoice->createSenderContact();
                      $invoice->senderFindArea();
                      $invoice->senderFindStreet();
                      $invoice->createSenderAddress();
                      $invoice->newFindRecipientArea();
                      $invoice->findRecipientArea();
                      $invoice->createRecipient();
                      $invoice->howCosts();
                      $invoice->order_id = $order_data["id"];
                      $invoice->createInvoice();



                      $order_id = $order_data["id"];

                      //add note

                      if (isset($order_id)) {
                          $order = wc_get_order($order_id);
                          $note = "Номер накладної: " . $_SESSION['invoice_id_for_order'];
                          $order->add_order_note($note);
                          $order->save();

                          unset($_SESSION['invoice_id_for_order']);
                      }
                  }
?>
