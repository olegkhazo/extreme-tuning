<?php
require_once("functions.php");
require_once 'api.php';
require_once 'morkvajustin-invoice.php';
// require 'createttn.php';

mnp_display_nav(); ?>

<?php
if (!isset($_SESSION)) {
    session_start();
}

//set order id if  HTTP REFFERRER  is woocommerce order
if (isset($_SERVER['HTTP_REFERER'])) {
    $qs = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY);
    if (!empty($qs)) {
        parse_str($qs, $output);
        // TODO check for key existence
        if (isset($output['post'])) {
            $order_id =  $output['post'];  // id
        }
    }
}

//if isset order from previous step id and not null srialize order id to session
//else do  not show ttn form
if (isset($order_id) && ($order_id != 0) &&  wc_get_order($order_id)) {
    $order_data0 = wc_get_order($order_id);
    if (isset($order_data0) && (!$order_data0 == false)) {
        $order_data = $order_data0->get_data();
        $_SESSION['order_id'] = serialize($order_id);
    } else {
        $showpage =false;
    }
}

//if isset order id only from session  get it
elseif (isset($_SESSION['order_id'])) {
    //$order_id = 0;
    $ret = @unserialize($_SESSION['order_id']);
    if (gettype($ret) == 'boolean') {
        $order_id = $_SESSION['order_id'];
    } else {
        $order_id = unserialize($_SESSION['order_id']);
    }
    if (wc_get_order($order_id)) {
        $order_data0 = wc_get_order($order_id);
        $order_data = $order_data0->get_data();
    }
    // echo '<pre>';
    // print_r($order_data);
    // echo '</pre>'
}
//else do not show form ttn
else {
    $showpage =false;
}
if(!isset($order_data['id'])){
  $order_data = [
    'id'=> null,
    'billing'=>[
      'first_name'=>'',
      'last_name' =>'',
      'city'=>'',
      'phone'=>'',
      'address_1'=>''
    ]
  ];
}

echo '<pre>$order_data[billing][city]';
print_r($order_data['billing']['city']);
echo '</pre>';

if ( ! empty( $_POST['mrkvjs_create_ttn'] ) ) { // After 'Створити' button clicked
    $invoice_db_data = array();
    $sender_city_uuid = '';
    $justinapi = new JustinApi();

    if ( 'uk' == get_user_locale() ) {
        global $wpdb;
        $city_table_name = $wpdb->prefix . 'woo_justin_ua_cities';
        $city_sender = $wpdb->get_row( // Get city uuid from DB ukrainian name cities table
        	"
        	SELECT uuid
        	FROM {$city_table_name}
        	WHERE descr = '{$_POST['invoice_sender_city']}'
        	"
        );
echo '$city_sender: ';var_dump($city_sender);
        $sender_city_uuid = $city_sender->uuid;

        $invoice_db_data = array( // Create invoice data for DB
            'order_id' => $order_id,
            'order_invoice' => '',
            'invoice_ref' => ''
        );
    }

    if ( 'ru_RU' == get_user_locale() ) {
        $countryCode = 'RU';
        // require 'createttn.php';
        $cityUuidJson = $justinapi->getCity( $countryCode, $_POST['invoice_sender_city'] );
        $cityUuidObj = json_decode( $cityUuidJson );
echo '<br><pre>$cityUuidObj: ';print_r($cityUuidObj);echo '</pre>';
        $sender_city_uuid = $cityUuidObj->data[0]->fields->uuid;
        $invoice_db_data = array(
            'order_id' => $order_id,
            'order_invoice' => $justinApiTtn->data->number,
            'invoice_ref' => $sender_city_uuid
        );
    }

    if ( ! $sender_city_uuid ) {
        $sender_city_uuid = $_POST['invoice_sender_city_uuid'];
    }

    // Add new order custom field on 'Edit Order' admin page
    $meta_key = 'justin_recipient_city_uuid';
    $meta_values = get_post_meta( $order_id, $meta_key, true );
    if ( empty( $meta_values ) ) {
      	add_post_meta( $order_id, $meta_key, $sender_city_uuid, true );
    } else {
      	update_post_meta( $order_id, $meta_key, $sender_city_uuid, true) ;
    }
}

// echo '<pre>';
// print_r($order_data);
// echo '</pre>';.
// $cityUuidJson = $justinapi->getCity( $_POST['invoice_recipient_city'] );
// $cityUuidObj = json_decode( $cityUuidJson );
// $cityUuid = $cityUuidObj->data[0]->fields->uuid;
// var_dump('$cityUuid: '.$cityUuid);
?>
<!-- <h2>Функціонал на стадії тесування</h2> -->
<h2 class="np_order_data_h2">Замовлення № <?php echo $order_data['id']; ?></h2>
<div class="">
   <div class="container">
      <form class="form-invoice form-invoice-3cols"  method="post" name="invoice">
         <div id="messagebox" class="messagebox_show">
         </div>
         <?php  justin_formlinkbox($order_data['id']); ?>
         <div class="tablecontainer">
            <table class="form-table full-width-input">
               <tbody id="tb1">
                  <tr>
                     <th colspan="2">
                        <h3 class="formblock_title">Відправник</h3>
                        <div id="errors"></div>
                     </th>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="sender_name">Відправник (П. І. Б)</label>
                     </th>
                     <td>
                      <input style="display:text" type="text" id="sender_name" name="invoice_sender_name" class="input sender_name" value="<?php echo get_option('justin_names'); ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="sender_namecity">Місто</label>
                     </th>
                     <td>
                        <input id="sender_namecity" type="text" value="<?php echo get_option('woocommerce_morkvajustin_shipping_method_city_name'); ?>" readonly="" name="invoice_sender_city">
                        <input type="hidden" name="invoice_sender_city_uuid" value="<?php echo $sender_city_uuid; ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="sender_phone">Телефон</label>
                     </th>
                     <td>
                        <input type="text" id="sender_phone" name="invoice_sender_phone" class="input sender_phone" value="<?php echo get_option('justin_phone'); ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="invoice_description">Опис відправлення</label>
                     </th>
                     <td class="pb7">
                        <textarea type="text" id="invoice_description" name="invoice_description" class="input" minlength="1" required=""><?php echo get_option('justin_invoice_description'); ?></textarea>
                        <p id="error_dec"></p>
                     </td>
                  </tr>
               </tbody>
            </table>
            <table class="form-table full-width-input">
               <tbody>
                  <tr>
                     <th colspan="2">
                        <h3 class="formblock_title">Одержувач</h3>
                        <div id="errors"></div>
                     </th>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="recipient_name">Одержувач (П.І.Б)</label>
                     </th>
                     <td>
                        <input type="text" name="invoice_recipient_name" id="recipient_name" class="input" recipient_name="" value="<?php echo $order_data['billing']['first_name']." ".$order_data['billing']['last_name']; ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="recipient_city">Місто одержувача</label>
                     </th>
                     <td>
                        <input type="text" name="invoice_recipient_city" id="recipient_city" class="recipient_city" value="<?php echo $order_data['billing']['city']; ?>">
                        <input type="hidden" name="invoice_recipient_city_uuid" value="<?php //echo $cityUuid; ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="RecipientAddressName">відділення:</label>
                     </th>
                     <td>
                        <textarea name="addresstext"><?php echo $order_data['billing']['address_1']; ?></textarea>
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="recipient_phone">Телефон</label>
                     </th>
                     <td>
                        <input type="text" name="invoice_recipient_phone" class="input recipient_phone" id="recipient_phone" value="<?php echo $order_data['billing']['phone']; ?>">
                     </td>
                  </tr>
               </tbody>
            </table>

         </div>
         <div class="tablecontainer">
            <table class="form-table full-width-input">
               <tbody>
                  <tr>
                     <th colspan="2">
                        <h3 class="formblock_title">Параметри відправлення</h3>
                        <div id="errors"></div>
                     </th>
                  </tr>
                  <tr>
                     <th scope="row"><label>Запланована дата:</label></th>
                     <?php $today = date('Y-m-d'); ?>
                     <td><input type="date" name="invoice_datetime" value="<?php echo $today; ?>" min="<?php echo $today; ?>">
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="invoice_payer">Платник</label>
                     </th>
                     <td>
                        <select id="invoice_payer" name="invoice_payer">
                           <option value="Recipient" selected="">Отримувач</option>
                           <option value="Sender">Відправник</option>
                        </select>
                     </td>
                  </tr>
                  <tr>
                     <th scope="row"><label class="light" for="invoice_cargo_mass">Вага, кг</label></th>
                     <td><input type="text" name="invoice_cargo_mass" id="invoice_cargo_mass" value="1.2">
                     </td>
                  </tr>
                  <tr>
                     <td colspan="2">
                       <p class="light">Якщо залишити порожнім, буде використано мінімальне значення 0.5.</p>
                     </td>
                  </tr>
                  <tr>
                     <td class="pb0">
                        <label class="light" for="invoice_volumei">Об'єм, м<sup>3</sup></label>
                     </td>
                     <td class="pb0">
                        <input type="text" id="invoice_volumei" name="invoice_volume" value="0">
                     </td>
                  </tr>
                  <tr>
                     <td colspan="2">
                        <p></p>
                     </td>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="invoice_placesi">Кількість місць</label>
                     </th>
                     <td>
                        <input type="text" id="invoice_placesi" name="invoice_places" value="1">
                     </td>
                  </tr>
                  <input type="hidden" name="InfoRegClientBarcodes" value="13812">
                  <tr>
                     <th scope="row">
                        <label for="invoice_priceid">Оголошена вартість</label>
                     </th>
                     <td>
                        <input id="invoice_priceid" type="text" name="invoice_price" required="" value="42.00">
                     </td>
                  </tr>
                  <tr>
                     <th colspan="2">
                        <p class="light">Якщо залишити порожнім, плагін використає вартість замовлення</p>
                     </th>
                  </tr>
                  <tr>
                     <th scope="row">
                        <label for="invoice_redelivery">Наложений платіж</label>
                     </th>
                     <td>
                        <select class="invoice_redelivery" name="invoice_redelivery">
                          <option value="ON">є</option>
                          <option value="OFF">немає</option>
                        </select>
                     </td>
                  </tr>
               </tbody>
            </table>
            <table class="form-table full-width-input">
               <tbody>
                  <tr>
                     <td>
                        <input name="mrkvjs_create_ttn" type="submit" value="Створити" class="checkforminputs button button-primary" id="submit">
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
         <div>
            <?php require 'card.php' ; ?>
            <div class="clear"></div>
         </div>
      </form>
   </div>
</div>

<?php
// $justinInvoice = new MJS_Plugin_Invoice();
// $justinInvoice->order_id = $order_data["id"];
echo '$order_data[id]: ';var_dump($order_data['id']);
echo '$order_id: ';var_dump($order_id);
// echo '<br><pre>$order_data: ';print_r($order_data);echo '</pre>';
// var_dump($justinApiTtn);


if ( ! empty( $_POST['mrkvjs_create_ttn'] ) ) { // After 'Створити' button clicked
        global $wpdb;
//     $invoice_db_data = array();
//     $justinapi = new JustinApi();
//
//     if ( 'uk' == get_user_locale() ) {
//         $city_table_name = 'woo_justin_ua_cities';
//         global $wpdb;
//         $city_sender = $wpdb->get_row( // Get city uuid from DB ukrainian name cities table
//         	"
//         	SELECT uuid
//         	FROM {$city_table_name}
//         	WHERE descr = '{$_POST['invoice_sender_city']}'
//         	"
//         );
// echo '$city_sender: ';var_dump($city_sender);
//         $sender_city_uuid = $city_sender->uuid;
//
//         $invoice_db_data = array( // Create invoice data for DB
//             'order_id' => $order_id,
//             'order_invoice' => '',
//             'invoice_ref' => ''
//         );
//     }
//
//     if ( 'ru_RU' == get_user_locale() ) {
//         $countryCode = 'RU';
//         // require 'createttn.php';
//         $cityUuidJson = $justinapi->getCity( $countryCode, $_POST['invoice_recipient_city'] );
//         $cityUuidObj = json_decode( $cityUuidJson );
// echo '<br><pre>$cityUuidObj: ';print_r($cityUuidObj);echo '</pre>';
//         $cityUuid = $cityUuidObj->data[0]->fields->uuid;
//         $invoice_db_data = array(
//             'order_id' => $order_id,
//             'order_invoice' => $justinApiTtn->data->number,
//             'invoice_ref' => $cityUuid
//         );
//     }

    require 'create_senddata.php'; // Create array data for API Justin query
    $invoice_table_name = $wpdb->prefix . 'justin_ttn_invoices';

    // Create Justin invoice (ttn) for current order in API Justin
    $justinApiTtnObj = $justinapi->createTtn( get_option( 'morkvajustin_apikey' ), $senddata );
    $justinApiTtn = json_decode( $justinApiTtnObj );
    $order_invoice_number = $justinApiTtn->data->number;
    $order_invoice_ref = $justinApiTtn->data->ttn;
    // $invoice_db_data = array_push( array( // Create invoice data for DB
    //     'order_id' => $order_id,
    //     'order_invoice' => $order_invoice_number
    //     // 'invoice_ref' => $sender_city_uuid
    // ) );
    // $invoice_db_data['order_id'] = $order_id;
    $invoice_db_data['order_invoice'] = $order_invoice_number;
    $invoice_db_data['invoice_ref'] = $order_invoice_ref;

echo '<pre>';
echo '<br>'.gettype($justinApiTtn).' $justinApiTtn'. '<br>';print_r($justinApiTtn);
echo '<br>'.gettype($invoice_db_data).' $justinApiTtn'. '<br>';print_r($invoice_db_data);
echo "</pre>";

    // $orderid = 0;
    // if ( $order_data['id']  > 0 ) {
    //     $orderid = $order_data['id'];
    // }

    // Insert invoice data row in DB invoice table
    $wpdb->insert( $invoice_table_name, $invoice_db_data, ['%d', '%d', '%s'] );
    // $wpdb->replace( $invoice_table_name, $invoice_db_data, ['%d', '%d', '%s'] );

    // $this->invoice_id = $invoice_number;
    // $this->invoice_ref = $invoice_ref;
}
?>
