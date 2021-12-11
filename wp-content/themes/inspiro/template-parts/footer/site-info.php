<?php
/**
 * Displays footer site info
 *
 * @package Inspiro
 * @subpackage Inspiro_Lite
 * @since Inspiro 1.0.0
 * @version 1.0.0
 */

?>
<div class="site-info preFooter">
    <div class="infoFooter">
		<ul>
			<li class="firstLi">Информация</li>
			<li><a href="https://www.extremetuning.com.ua/about/">О нас</a></li>
			<li><a href="https://www.extremetuning.com.ua/shop/">Магазин</a></li>
			<li><a href="https://www.extremetuning.com.ua/blog">Блог</a></li>
			<li><a href="https://www.extremetuning.com.ua/optovikam/">Оптовым покупателям</a></li>
			<li><a href="https://www.extremetuning.com.ua/oplata-i-dostavka/">Оплата и доставка</a></li>
			<li><a href="https://www.extremetuning.com.ua/obmen-i-vozvrat/">Обмен и возврат</a></li>
			<li><a href="https://www.extremetuning.com.ua/oferta/">Договор публичной оферты</a></li>
			<!--<li><a href="https://www.extremetuning.com.ua/vacancies">Вакансии</a></li>-->
		</ul>
    </div>
	<div class="helpFooter">
		<ul>
			<li class="firstLi">Помощь и поддержка</li>
			<li><a href="https://www.extremetuning.com.ua/contact/">Связаться</a></li>
			<li>
			    <a href="https://telegram.im/@extreme_tuning"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/telegram.png"></a>
			    <a href="viber://chat?number=%2B380976021028"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/viber.png"></a>
			</li>
			<li class="phoneNumber">
			    <a href="tel:+380976021028"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/call.png">+38097 602 10 28</a>
			</li>
			<li class="phoneNumber"><a href="tel:+380955944927"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/call.png">+38095 594 49 27</a></li>
			<li class="emailFooter"><a href="mailto:info@extremetuning.com.ua">info@extremetuning.com.ua</a></li>
		</ul>
	</div>
	<div class="socialLinksFooter">
		<ul>
		  <li>
			<a href="https://www.instagram.com/extuning/">
			  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/instagram.png" alt="instagram">
			</a>
		  </li>
		  <li>
			<a href="https://www.facebook.com/ext.tuning/">
			  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/facebook.png" alt="facebook">
			</a>
		  </li>
		  <li>
			<a href="https://www.youtube.com/channel/UCri3lj2yscsgrfPk37DsWVQ">
			  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/youtube.png" alt="youtube">
			</a>
		  </li>
		  <li>
			<a href="https://www.pinterest.ru/extreme_tuning/">
			  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/pinterest.png" alt="pinterest">
			</a>
		  </li>
		  <li>
			<a href="https://twitter.com/ExtremeTuning3">
			  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/twitter.png" alt="twitter">
			</a>
		  </li>
		</ul>
		<a class="logoFooterHome" href="https://www.extremetuning.com.ua/"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/white_logo.png"></a>
		
	</div>
</div>

<div class="site-info">
	<?php
if (function_exists('the_privacy_policy_link')) {
    the_privacy_policy_link('', '<span role="separator" aria-hidden="true"></span>');
}
?>
	<span class="copyright">
		<span>
			<p>
				<?php
/* translators: %s: WordPress trademark */
printf(esc_html__('© «Extreme Tuning™»2019–2021 %s', ''), '');
?>
			</p>
		</span>
		<span>
			<?php esc_html_e('Разработано в', 'gct');?> <a href="<?php echo 'https://www.gct.company/'; ?>" target="_blank" rel="nofollow">GCT.company</a>
		</span>
	</span>
</div><!-- .site-info -->
