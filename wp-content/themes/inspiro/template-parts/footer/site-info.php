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
			<li><a href="http://localhost/gct/extremetuning/about/">О нас</a></li>
			<li><a href="http://localhost/gct/extremetuning/shop/">Магазин</a></li>
			<li><a href="http://localhost/gct/extremetuning/blog">Блог</a></li>
			<li><a href="http://localhost/gct/extremetuning/optovikam/">Оптовым покупателям</a></li>
			<li><a href="http://localhost/gct/extremetuning/">Вакансии</a></li>
		</ul>
    </div>
	<div class="helpFooter">
		<ul>
			<li class="firstLi">Помощь и поддержка</li>
			<li><a href="http://localhost/gct/extremetuning/contact/">Связаться</a></li>
			<li><a href="tel:+380631349272"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/call.png">+38063 134 92 72</a></li>
		</ul>
	</div>
	<div class="socialLinksFooter">
		<ul>
		  <li>
			<a href="">
			  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/linkedin.png" alt="linkedin">
			</a>
		  </li>
		  <li>
			<a href="">
			  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/facebook.png" alt="">
			</a>
		  </li>
		  <li>
			<a href="">
			  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/instagram.png" alt="">
			</a>
		  </li>
		  <li>
			<a href="">
			  <img src="<?php echo get_template_directory_uri(); ?>/assets/images/twitter.png" alt="">
			</a>
		  </li>
		</ul>
		<a class="logoFooterHome" href="http://localhost/gct/extremetuning"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/white_logo.png"></a>
		
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
