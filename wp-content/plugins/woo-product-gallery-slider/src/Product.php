<?php

namespace Prodcut_Gallery_Sldier;

class Product
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', [$this,'frontend_scripts'], 90);
        add_action('after_setup_theme', [$this,'remove_default_gallery_support'], 100);
        $this->hooks();
    }
    public function remove_default_gallery_support()
    {
        $wzoom = wpgs_get_option('zoom', 'wpgs_settings', 'false');
        if ($wzoom == 'false') {
            remove_theme_support('wc-product-gallery-zoom');
        } else {
            add_theme_support('wc-product-gallery-zoom');
        }
        
        remove_theme_support('wc-product-gallery-lightbox');
        remove_theme_support('wc-product-gallery-slider');
    }

    public function hooks()
    {
        remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
        remove_action('woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20);
        add_action('woocommerce_before_single_product_summary', [$this,'wpgs_product_image'], 20);
        add_action('woocommerce_product_thumbnails', [$this,'wpgs_product_thumbnails'], 20);
    }
    // Output the product image before the single product summary.
    public function wpgs_product_thumbnails()
    {
        require_once CIPG_PATH. '/inc/product-thumbnails.php';
    }
    // Output the product image before the single product summary.
    public function wpgs_product_image()
    {
        require_once CIPG_PATH. '/inc/product-image.php';
    }

    /**
     * Frontend styles/scripts
     *
     * @return void
     */
    public function frontend_scripts()
    {
        wp_enqueue_script('slick', CIPG_ASSETS. '/js/slick.min.js', array('jquery'), CIPG_VERSION, true);
        wp_enqueue_script('fancybox', CIPG_ASSETS. '/js/jquery.fancybox.min.js', array('jquery'), CIPG_VERSION, true);

        wp_register_script('wpgsjs', CIPG_ASSETS. '/js/wpgs.js', array(), CIPG_VERSION, true);
        
        
        // Enqueued script with localized data.
        wp_enqueue_script('wpgsjs');

        $warrows = wpgs_get_option('navIcon', 'wpgs_settings', 'true');
        $wzoom = wpgs_get_option('zoom', 'wpgs_settings', 'false');
        $wautoPlay = wpgs_get_option('autoPlay', 'wpgs_settings', 'false');
        $wslider_thubms = wpgs_get_option('thubms', 'wpgs_settings', '4');
        $wslider_mouse_draging = wpgs_get_option('mouse_draging', 'wpgs_settings', 'false');
        $wslider_infinity_loop = wpgs_get_option('infinity_loop', 'wpgs_settings', 'false');
        $wzoom_script = '';
        $slider_rtl = (is_rtl()) ? 'true' : 'false';
        $nextArrow = ($slider_rtl == 'false') ? "nextArrow:'<i class=\"flaticon-right-arrow\"></i>'," : '';
        $prevArrow = ($slider_rtl == 'false') ?" prevArrow:'<i class=\"flaticon-back\"></i>'," : '';

        if ($wzoom == 'true') {
            $wzoom_script ="
            jQuery('.wpgs-for img.attachment-shop_single').each(function () {
				var newImgSrc = jQuery(this).attr('data-zoom_src');
				jQuery(this)
                .wrap('<span style=\"display:inline-block\"></span>')
                .css('display', 'block')
                .parent()
                .zoom({url: newImgSrc});
				
			});
            jQuery('.woocommerce-product-gallery__image img').load(function () {
                var imageObj = jQuery('.woocommerce-product-gallery__image img');
                if (!(imageObj.width() == 1 && imageObj.height() == 1)) {
                    jQuery(this).parent().find('.zoomImg').remove();
                }
            });

            ";
        }

        $wpgs_sliderJs = "jQuery(document).ready(function(){
        jQuery('.wpgs-for').slick({
            slidesToShow:1,
            slidesToScroll:1,
            arrows:{$warrows},
            fade:false,
            rtl: $slider_rtl,
            infinite:{$wslider_infinity_loop},
            adaptiveHeight:true,
            autoplay:{$wautoPlay},
            draggable:{$wslider_mouse_draging},
           {$nextArrow} 
           {$prevArrow}       
            asNavFor:'.wpgs-nav',
          
        });
        jQuery('.wpgs-nav').slick({
            slidesToShow:{$wslider_thubms},
            slidesToScroll:1,
            asNavFor:'.wpgs-for',
            arrows:{$warrows},
            rtl: $slider_rtl,
            infinite:{$wslider_infinity_loop},
            focusOnSelect:true,
            responsive:[{
                breakpoint:767,
                settings:{
                    slidesToShow:3,
                    slidesToScroll:1,
                    draggable:true,
                    autoplay:false,
                    arrows:true
                }
            }],
        });

        {$wzoom_script}
        

      });";
        wp_add_inline_script('wpgsjs', $wpgs_sliderJs);


        wp_enqueue_style('slick', CIPG_ASSETS. '/css/slick.css', null, CIPG_VERSION);
        wp_enqueue_style('slick-theme', CIPG_ASSETS. '/css/slick-theme.css', null, CIPG_VERSION);
        wp_enqueue_style('fancybox', CIPG_ASSETS. '/css/jquery.fancybox.min.css', null, CIPG_VERSION);

        $color = wpgs_get_option('navColor', 'wpgs_settings', '#222');
        $fb_bg_color = wpgs_get_option('bg_color', 'wpgs_lightbox_settings', '#222');
        
        $custom_css = "
                .wpgs-nav .slick-active img{opacity:.7;transition:all ease-in .3s}.wpgs-nav .slick-current img{opacity:1}
                .wpgs-for .slick-arrow{position:absolute;z-index:1;cursor:pointer;top:50%;margin-top:-15px}
                .wpgs .slick-disabled{display:none;}
                .flaticon-right-arrow{right:0}
                .wpgs-for .slick-arrow::before,.wpgs-nav .slick-prev::before, .wpgs-nav .slick-next::before{color: {$color} !important;}
                .fancybox-bg{background: {$fb_bg_color} !important;}                        
                ";

        if (is_product()) {
            $twist_product        = new \WC_Product(get_the_ID());
            $attachment_ids = $twist_product->get_gallery_image_ids();

            if (count($attachment_ids)+1 <= $wslider_thubms) {
                $custom_css .= "
					.wpgs-nav .slick-track {
						transform: inherit !important;
					}
				";
            }
        }
        
       
        wp_add_inline_style('fancybox', $custom_css);

        wp_enqueue_style('flaticon-wpgs', CIPG_ASSETS. '/css/font/flaticon.css', null, CIPG_VERSION);
    }
}
