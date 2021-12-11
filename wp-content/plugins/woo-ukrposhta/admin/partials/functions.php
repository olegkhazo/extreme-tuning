<?php

function FunctionDecode($type, $value)
{
  if($type=='type'){
    $arrayvalues = array('W2W', 'W2D');

    $arraydescriptions = array('Склад - Склад', 'Склад - Двері');

    for($i = 0; $i < sizeof($arrayvalues); $i++ ){

      if($value == $arrayvalues[$i]){
      return $arraydescriptions[$i];
      }
    }
  }
  if($type='fail'){
    $arrayvalues = array('RETURN', 'RETURN_AFTER_7_DAYS', 'PROCESS_AS_REFUSAL');
    $arraydescriptions = array('Повернути через 14 днів', 'Повернути після безкоштовного зберігання', 'знищити відправлення');
    for($j = 0; $j < sizeof($arrayvalues); $j++ ){
      if($value == $arrayvalues[$j]){
      return $arraydescriptions[$j];
    }
    }
  }
}

function the_deletediv($string){
  echo '<div id="messagebox" class="messagebox_show updated" data="20" style="height: 0px;padding: 8px;margin-left: 0; transition: all1s ease;">
				<div class="sucsess-naklandna">
					<p>Відправлення '.$string.' успішно видалено</p>
				</div>
		</div>';
}

function the_upformlabel($string){
  echo "<th scope=row>
    <label>".$string."</label>
    </th>";
}

function formblock_title($string){
  echo "<tr>
    <th colspan=2>
      <h3 class=\"formblock_title\">".$string."</h3>
      <div id=\"errors\"></div>
    </th>
  </tr>";
}

function logg($string){
  echo '<script>console.log("'.$string.'");</script>';
}

function mup_display_nav() {//display nav bar

 $arr_of_pages = array(
   array(
     'slug' => 'morkvaup_plugin', 'label' => 'Налаштування'
   ),
   array(
     'slug' => 'morkvaup_invoice', 'label' => 'Нове відправлення'
   ),
   array(
     'slug' => 'morkvaup_invoices', 'label' => 'Мої відправлення'
   ),
   array(
     'slug' => 'morkvaup_about', 'label' => 'Про плагін'
   ),
 );

 echo "<nav class=\"newnaw nav-tab-wrapper woo-nav-tab-wrapper\">";

 $wrs_page = $_GET['page'];

 for($i=0; $i<sizeof($arr_of_pages); $i++){
   $echoclass = 'nav-tab';
   if($wrs_page == $arr_of_pages[$i]['slug']){
     $echoclass = 'nav-tab-active nav-tab';
   }
   echo '<a href=admin.php?page='.$arr_of_pages[$i]['slug'].' class="'.$echoclass.'">'.$arr_of_pages[$i]['label'].'</a>';
 }

 echo "</nav>";

}

function woo_setting_weihgt_unit() { // what weight unit is set?
  $wc_weihgt_unit = get_option( 'woocommerce_weight_unit' );
  switch ($wc_weihgt_unit) {
      case 'g':
          return $weight_coef = 1;
          break;
      case 'kg':
          return $weight_coef = 1000;
          break;
      case 'lbs':
          return $weight_coef = 453.59;
          break;
      case 'oz':
          return $weight_coef = 28.34;
          break;        
  }
  return false; 
}

function woo_name_weihgt_unit_translate() {
  return __( get_option( 'woocommerce_weight_unit' ), 'woocommerce' );
}

function error_dd($variable){
  error_log('RESULT = '); error_log(print_r($variable, true) );
}


 ?>
