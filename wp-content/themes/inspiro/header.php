<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Inspiro
 * @subpackage Inspiro_Lite
 * @since Inspiro 1.0.0
 * @version 1.0.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="facebook-domain-verification" content="4ugkvj5vm6pl7mshzla7uvfodjxcwu" />
	<meta name="p:domain_verify" content="b34a8ce4da6f697cb3863082a46c20a6"/>
	<meta name="yandex-verification" content="f7d5bed2c6dd43a0" />
	<link rel="shortcut icon" href="https://www.extremetuning.com.ua/wp-content/themes/inspiro/assets/images/white_logo.png" type="image/x-icon">
	<script src="//code-ya.jivosite.com/widget/6LUaYJor3x" async></script>
	<?php wp_head(); ?>
	
	<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-MV6TCRW');</script>
<!-- End Google Tag Manager -->
	
<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(83835796, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true,
        ecommerce:"dataLayer"
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/83835796" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
	
	<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MV6TCRW"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'inspiro' ); ?></a>

	<header id="masthead" class="site-header" role="banner">
		<?php get_template_part( 'template-parts/navigation/navigation', 'primary' ); ?>
	</header><!-- #masthead -->

	<?php
	// Display custom header only on first page.
	if ( isset( $paged ) && $paged < 2 ) {
		if ( is_front_page() && is_home() && has_header_image() ) { // Default homepage.
			get_template_part( 'template-parts/header/header', 'image' );
		} elseif ( is_front_page() && has_header_image() ) { // static homepage.
			get_template_part( 'template-parts/header/header', 'image' );
		} elseif ( is_page() && inspiro_is_frontpage() && has_header_image() ) {
			get_template_part( 'template-parts/header/header', 'image' );
		} elseif ( is_page_template( 'page-templates/homepage-builder-bb.php' ) && has_header_image() ) {
			get_template_part( 'template-parts/header/header', 'image' );
		}
	}
	?>

	<div class="site-content-contain">
		<div id="content" class="site-content">
