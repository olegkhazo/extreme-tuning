<?php
/**
 * Registering callbacks for settings admin page
 *
 * @link        http://morkva.co.ua/
 * @since       1.0.0
 *
 * @package     morkvaup-plugin
 * @subpackage  morkvaup-plugin/includes
 */
/**
 * Registering callbacks for settings admin page
 *
 *
 * @package    morkvaup-plugin
 * @subpackage morkvaup-plugin/includes
 * @author     MORKVA <hello@morkva.co.ua>
 */
 class MUP_Plugin_Callbacks {
 	public function adminDashboard()
	{
		return require_once( "$this->plugin_path/templates/admin.php" );
	}

	public function adminInvoice()
	{
		return require_once( "$this->plugin_path/templates/invoice.php" );
	}

	public function adminSettings()
	{
		return require_once( "$this->plugin_path/templates/taxonomy.php" );
	}

	public function morkvaupOptionsGroup( $input )
	{
		return $input;
	}

	public function morkvaupAdminSection()
	{
		echo 'Введіть свій API ключ для початку щоб плагін міг працювати.';
	}

  public function morkvaupAuthBearer()
	{
		$value = esc_attr( get_option( 'production_bearer_ecom' ) );
		echo '<input type="text" class="regular-text" name="production_bearer_ecom" value="' . $value . '" placeholder="API ключ">';
		echo '';
	}
  public function morkvaupProdBearer()
  {
    $value = esc_attr( get_option( 'production_bearer_status_tracking' ) );
    echo '<input type="text" class="regular-text" name="production_bearer_status_tracking" value="' . $value . '" placeholder="API ключ">';
    echo '';
  }
  public function morkvaupCpToken()
  {
    $value = esc_attr( get_option( 'production_cp_token' ) );
    echo '<input type="password" class="regular-text" name="production_cp_token" value="' . $value . '" placeholder="API ключ">';
    echo '';
  }
  public function morkvaupprinttype()
  {
    $value = esc_attr( get_option( 'proptype' ) );
    $values= array('p','p','p');
    for( $i=0; $i<sizeof($values); $i++){
      if( $i == $value){
        $values[$i] = 'selected';
      }

    }
    echo '
          <select  class="regular-text" name="proptype">
            <option '.$values[0].' value="0">100*100 мм</option>
            <option '.$values[1].' value="1">100*100 мм для друку на форматі А4</option>
            <option '.$values[2].' value="2">100*100 мм для друку на форматі А5</option>
          </select>';
    echo '<p></p>';
  }
  public function morkvaupzone(){
  $activate = get_option( 'zone_ukrposhta' );

  $checked = $activate;
  $current = 1;
  $echo = false;
  echo '<input '. $activate .' type="checkbox" class="regular-text" name="zone_ukrposhta" value="1" ' . checked($checked, $current, $echo) . ' />За замовчуванням зони не використовуються. Проте якщо вам потрібно настроїти зони доставки, використовуйте цей пункт.<p>Якщо після настройок <a href="admin.php?page=wc-settings&tab=shipping">тут</a> не відображається метод доставки при оформленні замовлення вимкніть цей пункт</p>';
}

    public function morkvaupsendtype()
  {
    $value = esc_attr( get_option( 'sendtype' ) );
    $values= array('p','p');
    $sendtypes = array('EXPRESS', 'STANDARD');
    for( $i=0; $i<sizeof($values); $i++){
      if( $sendtypes[$i] == $value){
        $values[$i] = 'selected';
      }

    }
    echo '
          <select  class="regular-text" name="sendtype">
            <option '.$values[0].' value="EXPRESS">EXPRESS</option>
            <option '.$values[1].' value="STANDARD">STANDARD</option>
          </select>';
    echo '<p></p>';
  }

  // public function morkvaupsendwtype()
  // {
  //   $value = esc_attr( get_option( 'sendwtype' ) );
  //   $values= array('p','p');
  //   $sendtypes = array('W2W', 'W2D');
  //   for( $i=0; $i<sizeof($values); $i++){
  //     if( $sendtypes[$i] == $value){
  //       $values[$i] = 'selected';
  //     }

  //   }
  //   echo '
  //         <select  class="regular-text" name="sendwtype">
  //           <option '.$values[0].' value="W2W">Відділення - Відділення</option>
  //           <option '.$values[1].' value="W2D">Відділення - Двері</option>
  //         </select>';
  //   echo '<p></p>';
  // }

  public function morkvaupActivateen() {
		$activate = get_option( 'activate_plugin_en' );
		$checked = $activate;
		$current = 1;
		$echo = false;
		echo '<input type="checkbox" class="regular-text" name="activate_plugin_en" value="1" ' . checked($checked, $current, $echo) . ' /><p>Якщо не обрати цей пункт, створення накладної Укрпошти буде доступне лише для методу доставки Укрпошти</p>';
	}
  public function morkvaupukrposhta_calculate_rates() {
    $activate = get_option( 'ukrposhta_calculate_rates' );
    $checked = $activate;
    $current = 1;
    $echo = false;
    echo '<input type="checkbox" class="regular-text" name="ukrposhta_calculate_rates" value="1" ' . checked($checked, $current, $echo) . ' />
    <p>Ціна доставки буде розраховуватись і додаватись на сторінці Оформлення замовлення. 
    Дані беруться з <a target="_blank" href="https://ukrposhta.ua/ua/taryfy-ukrposhta-standart">тарифної таблиці</a>.<br>
    Для розрахунку міжнародної доставки потрібні ключі API,</p>';
  }





	public function morkvaupmorkva_ukrposhta_default_price() {
		$phone = esc_attr( get_option( 'morkva_ukrposhta_default_price' ) );
		echo '<input type="text" class="regular-text" name="morkva_ukrposhta_default_price" value="' . $phone . '" placeholder="">';
    echo '<p>Вказане тут значення буде додаватись до ціни замовлення замість розрахунку ціни ("Додавати вартість доставки до суми замовлення?").<br>Ціна вказується у валюті, встановленій <a target="_blank" href="admin.php?page=wc-settings&tab=general"> в налаштуваннях WooСommerce</a>.</p>';
	}

	public function morkvaupPhone() {
		$phone = esc_attr( get_option( 'phone' ) );
		echo '<input type="text" class="regular-text" name="phone" value="' . $phone . '" placeholder="0901234567">';
		echo '<p>Підказка: основний формат 0987654321 (без +38)</p>';
	}

  public function morkvaupEdrpou() {
    $edrpou = esc_attr( get_option( 'edrpou' ) );
    echo '<input type="text" class="regular-text" name="edrpou" value="' . $edrpou . '" placeholder="Вісім цифр">';
  }

  public function morkvaupTin() {
    $up_tin = esc_attr( get_option( 'up_tin' ) );
    echo '<input type="text" class="regular-text" name="up_tin" value="' . $up_tin . '" placeholder="Десять цифр">';
  }   

  public function morkvaup_sender_type() {
    $senderValue = get_option( 'up_sender_type' );
    $senderValues = array( 'INDIVIDUAL' , 'COMPANY', 'PRIVATE_ENTREPRENEUR' );
    $senderTypeChoice = array( 'Фізичну особу', 'Юридичну особу', 'Фізичну особу-підприємця (ФОП)' );
    $addSelectedSender = array( ' ', ' ', ' ' );
    for ( $i = 0; $i < sizeof( $senderValues ); $i++ ){
        if ( $senderValues[$i] == $senderValue ){
          $addSelectedSender[$i] = 'selected';
        }
    }
    echo '<select ' . $senderValue . ' id="up_sender_type" name="up_sender_type">';
    echo '<option value="">Ваш вибір...</option>';
    for( $i = 0; $i < sizeof( $senderValues ); $i++) {
        echo '<option '. $addSelectedSender[$i] . ' value="' . $senderValues[$i] . '">' . $senderTypeChoice[$i] . '</option>';
    }
    echo '</select>';
  }

  public function morkvaupCompanyName() {
    $up_company_name = esc_attr( get_option( 'up_company_name' ) );
    echo '<input type="text" class="regular-text" name="up_company_name" value="' . $up_company_name . '" placeholder="Не більше 60 символів">';
  }  

	public function morkvaupNames() {
		$names = esc_attr( get_option( 'names1' ) );
		echo '<input type="text" class="regular-text" name="names1" value="' . $names . '" placeholder="Петренко">';
	}

  public function morkvaupCityLatin() {
    $names = esc_attr( get_option( 'citylatin' ) );
    echo '<input type="text" class="regular-text" name="citylatin" value="' . $names . '" placeholder="Kyiv">';
  }

  public function morkvaupStreetLatin() {
    $names = esc_attr( get_option( 'streetlatin' ) );
    echo '<input type="text" class="regular-text" name="streetlatin" value="' . $names . '" placeholder="street">';
  }

  public function morkvaupNumLatin() {
    $names = esc_attr( get_option( 'numlatin' ) );
    echo '<input type="text" class="regular-text" name="numlatin" value="' . $names . '" placeholder="Kyiv">';
  }
  public function morkvaupNamesLatin() {
    $names = esc_attr( get_option( 'nameslatin' ) );
    echo '<input type="text" class="regular-text" name="nameslatin" value="' . $names . '" placeholder="Петренко Петро Петрович">';
  }

  public function morkvaupNames2() {
    $names = esc_attr( get_option( 'names2' ) );
    echo '<input type="text" class="regular-text" name="names2" value="' . $names . '" placeholder="Петро">';
  }
  public function morkvaupNames3() {
    $names = esc_attr( get_option( 'names3' ) );
    echo '<input type="text" class="regular-text" name="names3" value="' . $names . '" placeholder="Петрович">';
  }

	public function morkvaupFlat() {
		$flat = esc_attr( get_option( 'flat' ) );
		echo '<input type="text" class="regular-text" name="flat" value="' . $flat . '" placeholder="номер">';
	}

	public function morkvaupWarehouseAddress()
	{
		$warehouse = esc_attr( get_option( 'woocommerce_store_postcode' ) );


		echo '<input type="text" class="regular-text" name="warehouse" value="' . $warehouse . '" placeholder="Франка 14" readonly>';
		echo '<p>Налаштування цього поля беруться із <a href="admin.php?page=wc-settings&tab=general">налаштувань Woocommerce </a></p>';
	}

	public function morkvaupInvoiceDescription()
	{
		$invoice_description = get_option('up_invoice_description');

    echo '<textarea  id=td45 name="up_invoice_description" rows="5" cols="54">' . $invoice_description . '</textarea>
<span id=sp1 class=shortspan>+ Вартість</span>
<select class=shortspan id=shortselect>
  <option value="0" disabled selected style="display:none"> + Перелік</option>
  <option value="list" > + Перелік товарів (з кількістю)</option>
  <option value="list_qa"> + Перелік товарів ( з артикулами та кількістю)</option>
</select>
<select class=shortspan id=shortselect2>
  <option value="0" disabled selected style="display:none"> + Кількість</option>
  <option value="qa"> + Кількість позицій</option>
  <option value="q"> + кількість товарів</option>
</select>
<p>значення шорткодів, при натисненні кнопок додаються в кінець текстового поля</p>
';

    $path = MUP_PLUGIN_PATH . 'public/partials/morkvaup-plugin-invoices-page.php';
		if(!file_exists($path)){
		 echo '<p>Функція опису за промовчанням працює у PRO версії. у Free потрібно буде заповнювати опис кожного відправлення вручну.</p>';
		}
	}

	public function morkvaupInvoiceWeight()
	{
		$activate = get_option( 'invoice_weight' );
		$checked = $activate;
		$current = 1;
		$echo = false;
		echo '<input type="checkbox" class="regular-text" name="invoice_weight" value="1" ' . checked($checked, $current, $echo) . ' />';
	}

	public function morkvaupEmailTemplate()
	{
		$content = get_option( 'morkvaup_email_template' );
		$editor_id = 'morkvaup_email_editor_id';

		wp_editor( $content, $editor_id, array( 'textarea_name' => 'morkvaup_email_template' ) );
	}
	public function morkvaupEmailSubject()
	{
		$subject = get_option( 'morkvaup_email_subject' );

		echo '<input type="text" name="morkvaup_email_subject" class="regular-text" value="' . $subject . '" />';
	}



 }
