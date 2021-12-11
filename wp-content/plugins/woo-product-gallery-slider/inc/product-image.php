<?php

/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.1
 */

defined('ABSPATH') || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if (! function_exists('wc_get_gallery_image_html')) {
    return;
}

global $product;

$post_thumbnail_id = $product->get_image_id();
$image         = wp_get_attachment_image($post_thumbnail_id, 'shop_single', true, array(
    "class" => "attachment-shop_single size-shop_single wp-post-image",
    "data-zoom_src" => wp_get_attachment_image_src($post_thumbnail_id, apply_filters('gallery_slider_zoom_image_size', 'full'))[0],
));


$wrapper_classes = apply_filters('woocommerce_single_product_image_gallery_classes', array(
    'wpgs',
    'woocommerce-product-gallery',
    'wpgs--' . (has_post_thumbnail() ? 'with-images' : 'without-images'),
    'images',

));
$slider_rtl = (is_rtl()) ? 'true' : 'false';

do_action('wpgs_before_image_gallery');

?>

<div class="<?php echo esc_attr(implode(' ', array_map('sanitize_html_class', $wrapper_classes))); ?>" <?php echo esc_attr($slider_rtl == 'true' ? 'dir=rtl'  : '');?> >

		<?php
      
if (has_post_thumbnail()) {
    echo '<div class="wpgs-for">';
    $attachment_ids = $product->get_gallery_image_ids();

    $lightbox_src = wc_get_product_attachment_props($post_thumbnail_id);
    $img_caption = '';
    if (wpgs_get_option('caption', 'wpgs_lightbox_settings', 'true') == 'true') {
        $img_caption = (empty(wp_get_attachment_caption($post_thumbnail_id))) ? get_the_title($post_thumbnail_id) : wp_get_attachment_caption($post_thumbnail_id);
    }
   
    echo '<div  class="woocommerce-product-gallery__image single-product-main-image"><a
    data-caption="'.$img_caption.'" 
    data-fancybox="wpgs-lightbox" href="'.$lightbox_src['url'].'" 
    data-mobile=["clickContent:close","clickSlide:close"]  
    data-hash="false" >' . $image . '</a></div>';

    if ($attachment_ids) {
        foreach ($attachment_ids as $attachment_id) {
            $thumbnail_image     = wp_get_attachment_image($attachment_id, 'shop_single', true, ["data-zoom_src" => wp_get_attachment_image_src($attachment_id, apply_filters('gallery_slider_zoom_image_size', 'full'))[0],]);
            $lightbox_src = wc_get_product_attachment_props($attachment_id);
            if (wpgs_get_option('caption', 'wpgs_lightbox_settings', 'true') == 'true') {
                $img_caption = (empty(wp_get_attachment_caption($attachment_id))) ? get_the_title($attachment_id) : wp_get_attachment_caption($attachment_id);
            }


            // fw_print($thumbnail_src);
            echo '<div><a data-fancybox="wpgs-lightbox" 
            data-caption="'.$img_caption.'" 
            href="'.$lightbox_src['url'].'" 
            data-thumb="'.wp_get_attachment_image_url($attachment_id, apply_filters('wpgs_new_thumb_img_size', 'woocommerce_gallery_thumbnail')).'" 
            data-mobile=["clickContent:close","clickSlide:close"]
            data-hash="false"
            >' . $thumbnail_image . '</a></div>';
        }
    }
    echo "</div>";
} else {
    $html = '<div class="woocommerce-product-gallery__image--placeholder">';
    $html .= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src()), esc_html__('Awaiting product image', 'woocommerce'));
    $html .= '</div>';
}

//echo apply_filters('woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id);

do_action('woocommerce_product_thumbnails');
?>

</div>
