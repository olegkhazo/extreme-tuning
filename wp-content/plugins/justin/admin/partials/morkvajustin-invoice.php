<?php

/**
 * Providing invoice functions for plugin
 *
 *
 * @link       http://morkva.co.ua
 * @since      1.0.0
 *
 * @package    morkvajustin-plugin
 * @subpackage morkvajustin-plugin/public/partials
 */

class MJS_Plugin_Invoice extends JustinApi {

    public $apikey;

	public $order_id;

	public $invoice_id;

	public $invoice_ref;

    public function __construct()
    {
        // global $wpdb;
        // $table_name = $wpdb->prefix . 'justin_ttn_invoices';
        //
        // // $justin_invoice_number = $obj["data"][0]["IntDocNumber"];
        // // $invoice_ref = $obj["data"][0]["Ref"];
        //
        // $orderid = 0;
        // if ( $this->order_id  > 0 ) {
        //     $orderid = $this->order_id;
        // }
        // $wpdb->insert(
        //     $table_name,
        //     array(
        //         'order_id' => $orderid,
        //         'order_invoice' => $justin_invoice_number,
        //         'invoice_ref' => $invoice_ref
        //     )
        // );
        // $this->invoice_id = $justin_invoice_number;
        // $this->invoice_ref = $invoice_ref;
    }

}
