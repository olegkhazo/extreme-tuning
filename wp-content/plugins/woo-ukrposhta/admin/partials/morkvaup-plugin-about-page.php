<?php

require("functions.php");

?>
<?php 	mup_display_nav(); ?>

					<div class="container">
					<?php if (isset($_GET['calc'])){
						echo 'calculating';
						require 'api.php';

						$params = array(
			        "packageType"=>get_option('senduptype'),
			        "weight"=> 1,
			        "currencyExchangeRate"=>"1",
			        "currencyCode"=>"UAH",
			        "recipientCountryIso3166"=>"US",
			        "declaredPrice"=>0,
			        "recommended"=> true,
			        "bulky"=>false,
			        "avia"=>false,
			        "withDeliveryNotification"=>false,
			        "byCourier"=>false,
			        "w2d"=>false,
			        "cancelOrChange"=>false,
			        "length"=>10,
			        "transportType"=>"AVIA"
			      );


						$bearer = get_option('production_bearer_ecom');
						$cptoken = get_option('production_cp_token');
						$tbearer = get_option('production_bearer_status_tracking');

						$ukrposhtaApi = new UkrposhtaApi($bearer ,$cptoken, $tbearer);
						$invoice = $ukrposhtaApi->howcosts( $params );

			      echo '<pre><hr>';
						print_r(	$invoice);

			      echo '</pre><hr>';
					}?>
					<div class="row">
						<h1><?php echo MUP_PLUGIN_NAME; ?></h1>
						<div id="tab " class="tab-pane" >
							<div>
							<p>
								Плагін дозволяє генерувати накладну Укрпошти я з даних про клієнта на основі <a href="http://dev2.morkva.co.ua/wp-content/plugins/woo-ukrposhta-pro/admin/partials/getapi.pdf">API</a> по Україні, та міжнародних відправлень на основі  <a href="http://dev2.morkva.co.ua/wp-content/plugins/woo-ukrposhta-pro/admin/partials/getapi.pdf?international=true">  API для міжнародних відправлень.</a>.
							</p>
						</div>
						<div>
							<h2>
								Налаштування
							</h2>
							<p>

							</p>
Для роботи плагіну потрібно отримати АРІ-ключі від Укрпошти (укласти угоду на відділенні). Деталі: <a href="https://ukrposhta.ua/ukrposhta-dlya-biznesu.html">ukrposhta.ua/ukrposhta-dlya-biznesu</a>
							<p>
								<li>1.Встановіть плагін через меню Plugins</li>
								<li>2.Введіть ключі від Укрпошти</li>
								<li>3.Введіть реквізити Відправника</li>
							</p>
						</div>
						<div>
							<h2>
								Як згенерувати накладну на відправлення?
							</h2>
							<p>

								<li>1. Натисніть “Створити відправлення” на сторінці замовлення</li>
								<li>2. Введіть вагу</li>
								<li>3. Введіть довжину посилки</li>
								<li>4. Виберіть платника</li>
								<li>5. Введіть суму післяплати ("наложка")</li>
								<li>6. Натисніть “Згенерувати”</li>
							</p>

						</div>
						<div>
							<h2>
								Потрібно більше функцій?
							</h2>
							<p>
								Напишіть нам: hello@morkva.co.ua
							</p>
						</div>

						<div>
							<h2>
								Підтримка
							</h2>
							<p>
								Виникли проблеми з плагіном? Пишіть нам на support@morkva.co.ua<br />
				Або на нашу сторінку у ФБ: <a href="https://www.facebook.com/morkvasite/">https://www.facebook.com/morkvasite/</a><br />


							</p>
						</div>
						</div>
