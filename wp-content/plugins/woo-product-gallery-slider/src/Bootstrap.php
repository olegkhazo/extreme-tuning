<?php

namespace Prodcut_Gallery_Sldier;

class Bootstrap
{
    public function __construct()
    {
        new Product();
        new Settings();
        add_action('admin_enqueue_scripts', [$this,'admin_scripts'], 90);
        add_action('admin_notices', [$this,'getdfwc'], 10);
        if (time() > strtotime(get_option('ciwpgs_installed').' + 3 Days')) {
            add_action('admin_notices', [$this,'review'], 10);
        }
        if (time() > strtotime(get_option('ciwpgs_installed').' + 2 Days')) {
            add_action('admin_notices', [$this,'dfwc_banner'], 10);
        }
        add_action('admin_init', [$this,'wcpg_param_check'], 10);
        add_action('wcpg_admin_top', [$this,'pro_notice'], 10);
        
        add_action('plugin_action_links_'.CIPG_FILE, [$this,'wpgs_plugin_row_meta'], 90);
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
     * Admin scripts/styles
     *
     * @return void
     */
    public function admin_scripts()
    {
    }
    
    public function getdfwc()
    {
        $slug        = 'deposits-for-woocommerce';
        $install_url = esc_url(wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $slug), 'install-plugin_' . $slug));
                    
        $popup_url = esc_url(add_query_arg(array(
            'tab'       => 'plugin-information',
            'section'   => 'description',
            'plugin'    => $slug ,
            'TB_iframe' => 'true',
            'width'     => '950',
            'height'    => '600',
        ), self_admin_url('plugin-install.php')));

        $promo_message = '<img src="https://ps.w.org/deposits-for-woocommerce/assets/icon-128x128.png" /> <p>Allowed customers to pay for products using a fixed or percentage amount of the productby using <a target="_blank" class="thickbox open-plugin-details-modal" href="'.$popup_url.'"><strong>Deposits for WooCommerce</strong></a></p><a href="'.$install_url.'" class="button button-alt"> Install Now </a>';
      
        $dismiss_parm = array( 'wcpg-dfwc-dismiss' => '1' );
        if (get_option('wcpg-dfwc-dismiss')) {
            return;
        } ?>
        <div class="notice notice-success notice-dfwc-is-here">
        <?php echo $promo_message; ?>
        <a href="<?php echo esc_url(add_query_arg($dismiss_parm)); ?>" class="dfwc-dismiss button button-link"><?php _e('[X] Hide Notification', 'wpgs'); ?></a>
        </div>
        <style>
            .notice-dfwc-is-here {
                display: flex;
                align-items: center;
                padding: 0;

            }
           .notice-dfwc-is-here img {
                width: 55px;
                margin-right: 10px;
            }
            .dfwc-dismiss {
                position: absolute;
                right: 30px;
                font-size: 12px;
            }
            .notice-dfwc-is-here .button-alt{
                margin-left: 15px;
            }
        </style>
    <?php
    }
    /**
     * Leave Review Notice
     *
     * @return void
     */
    public function review()
    {
        $dismiss_parm = array( 'wcpg-review-dismiss' => '1' );
        $ciwg_maybelater = array( 'wcpg-later-dismiss' => '1' );
        
        if (get_option('wcpg_plugin_review') || get_transient('wpgs-review-later')) {
            return;
        } ?>
        <div class="notice ciplugin-review">
        <p><img draggable="false" class="emoji" alt="🎉" src="https://s.w.org/images/core/emoji/11/svg/1f389.svg"><strong style="font-size: 19px; margin-bottom: 5px; display: inline-block;" ><?php echo __('Thanks for using Product gallery slider for WooCommerce.', 'wpgs'); ?></strong><br> <?php _e('If you can spare a minute, please help us by leaving a 5 star review on WordPress.org.', 'wpgs'); ?></p>
        <p class="dfwc-message-actions">
            <a style="margin-right:5px;" href="https://wordpress.org/support/plugin/woo-product-gallery-slider/reviews/#new-post" target="_blank" class="button button-primary button-primary"><?php _e('Happy To Help', 'wpgs'); ?></a>
            <a style="margin-right:5px;" href="<?php echo esc_url(add_query_arg($ciwg_maybelater)); ?>" class="button button-alt"><?php _e('Maybe later', 'wpgs'); ?></a>
            <a href="<?php echo esc_url(add_query_arg($dismiss_parm)); ?>" class="dfwc-button-notice-dismiss button button-link"><?php _e('Hide Notification', 'wpgs'); ?></a>
        </p>
        </div>
        <?php
    }
    public function pro_notice()
    { ?>
        <div class="notice notice-success">
        <h3>Need More options for customize the gallery slider ?</h3>
        <p style="font-size:16px">Get fully customizable image gallery slider for the product page.<br> comes with vertical and horizontal gallery layouts, clicking, sliding, image navigation, fancybox 3 & many more exciting features.</p>
        <a href="https://codeixer.com/twist" target="_blank" class="button button-primary" style="margin-bottom:20px">Know more about the pro version</a>
        </div>
        
        <?php
    }
    public function dfwc_banner()
    {
        if (get_option('ci_woo_deposits_installed') || get_option('dfwc-banner')) {
            return;
        }
        $dismiss_parm = array( 'dfwc-banner' => '1' );
        $slug        = 'deposits-for-woocommerce';
        $install_url = esc_url(wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $slug), 'install-plugin_' . $slug));
                    
        $popup_url = esc_url(add_query_arg(array(
            'tab'       => 'plugin-information',
            'section'   => 'description',
            'plugin'    => $slug ,
            'TB_iframe' => 'true',
            'width'     => '950',
            'height'    => '600',
        ), self_admin_url('plugin-install.php')));

        $promo_message = '<img src="https://ps.w.org/deposits-for-woocommerce/assets/icon-128x128.png" /> <p>Allowed customers to pay for products using a fixed or percentage amount of the productby using <a target="_blank" class="thickbox open-plugin-details-modal" href="'.$popup_url.'"><strong>Deposits for WooCommerce</strong></a></p>
        <a href="'.$install_url.'" class="button button-primary"> Install Now </a><a href="'.esc_url(add_query_arg($dismiss_parm)).'" class="button button-link"> Hide Notice [x]</a>'; ?>
        <div id="wptrt-notice-woo_deposits" class="notice notice-success">
        <?php echo $promo_message; ?>
        </div>
        <?php
    }
    /**
     * simple dismissable logic
     *
     * @return void
     */
    public function wcpg_param_check()
    {
        if (isset($_GET['wcpg-review-dismiss']) && $_GET['wcpg-review-dismiss'] == 1) {
            update_option('wcpg_plugin_review', 1);
        }
        if (isset($_GET['wcpg-dfwc-dismiss']) && $_GET['wcpg-dfwc-dismiss'] == 1) {
            update_option('wcpg-dfwc-dismiss', 1);
        }
        if (isset($_GET['dfwc-banner']) && $_GET['dfwc-banner'] == 1) {
            update_option('dfwc-banner', 1);
        }
        if (isset($_GET['wcpg-later-dismiss']) && $_GET['wcpg-later-dismiss'] == 1) {
            set_transient('wpgs-review-later', 1, 2*DAY_IN_SECONDS);
        }
    }
}
