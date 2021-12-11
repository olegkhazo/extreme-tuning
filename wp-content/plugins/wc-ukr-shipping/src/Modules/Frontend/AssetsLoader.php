<?php

namespace kirillbdev\WCUkrShipping\Modules\Frontend;

use kirillbdev\WCUkrShipping\Services\AddressService;
use kirillbdev\WCUkrShipping\Services\TranslateService;
use kirillbdev\WCUkrShipping\Traits\StateInitiatorTrait;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;

if ( ! defined('ABSPATH')) {
    exit;
}

class AssetsLoader implements ModuleInterface
{
    use StateInitiatorTrait;

    private $translateService;

    public function __construct(TranslateService $translateService)
    {
        $this->translateService = $translateService;
    }

    public function init()
    {
        add_action('wp_head', [ $this, 'loadCheckoutStyles' ]);
        add_action('wp_head', [ $this, 'initState' ]);
        add_action('wp_enqueue_scripts', [ $this, 'loadFrontendAssets' ]);
    }

    public function loadFrontendAssets()
    {
        if (!wc_ukr_shipping_is_checkout()) {
            return;
        }

        wp_enqueue_style(
            'wc_ukr_shipping_css',
            WC_UKR_SHIPPING_PLUGIN_URL . 'assets/css/style.min.css'
        );

        if ((int)get_option('wcus_checkout_new_ui')) {
            wp_enqueue_script(
                'wcus_checkout_js',
                WC_UKR_SHIPPING_PLUGIN_URL . 'assets/js/checkout2.min.js',
                [ 'jquery' ],
                filemtime(WC_UKR_SHIPPING_PLUGIN_DIR . 'assets/js/checkout2.min.js'),
                true
            );
        }
        else {
            wp_enqueue_script(
                'wcus_checkout_js',
                WC_UKR_SHIPPING_PLUGIN_URL . 'assets/js/checkout.min.js',
                ['jquery'],
                filemtime(WC_UKR_SHIPPING_PLUGIN_DIR . 'assets/js/checkout.min.js'),
                true
            );
        }

        $this->injectGlobals();
    }

    public function loadCheckoutStyles()
    {
        if (!wc_ukr_shipping_is_checkout()) {
            return;
        }

        ?>
      <style>
          .wc-ukr-shipping-np-fields {
              padding: 1px 0;
          }

          .wcus-state-loading:after {
              border-color: <?= get_option('wc_ukr_shipping_spinner_color', '#dddddd'); ?>;
              border-left-color: #fff;
          }
      </style>
        <?php
    }

    private function injectGlobals()
    {
        $translator = $this->translateService;
        $translates = $translator->getTranslates();
        $addressService = new AddressService();

        $globals = [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'homeUrl' => home_url(),
            'lang' => $translator->getCurrentLanguage(),
            'nonce' => wp_create_nonce('wc-ukr-shipping'),
            'disableDefaultBillingFields' => apply_filters('wc_ukr_shipping_prevent_disable_default_fields', false) === false ?
                1 :
                0,
            'options' => [
                'address_shipping_enable' => (int)wc_ukr_shipping_get_option('wc_ukr_shipping_address_shipping')
            ]
        ];

        if ((int)get_option('wcus_checkout_new_ui')) {
            $globals['default_cities'] = $addressService->getDefaultCities();
            $globals['i18n'] = [
                'fields_title' => __('checkout_fields_title', WCUS_TRANSLATE_DOMAIN),
                'shipping_type_warehouse' => __('checkout_shipping_type_warehouse', WCUS_TRANSLATE_DOMAIN),
                'shipping_type_doors' => __('checkout_shipping_type_doors', WCUS_TRANSLATE_DOMAIN),
                'ui' => [
                    'city_placeholder' => __('checkout_city_placeholder', WCUS_TRANSLATE_DOMAIN),
                    'warehouse_placeholder' => __('checkout_warehouse_placeholder', WCUS_TRANSLATE_DOMAIN),
                    'custom_address_placeholder' => __('checkout_custom_address_placeholder', WCUS_TRANSLATE_DOMAIN),
                    'text_search' => __('checkout_text_search', WCUS_TRANSLATE_DOMAIN),
                    'text_loading' => __('checkout_text_loading', WCUS_TRANSLATE_DOMAIN),
                    'text_more' => __('checkout_text_more', WCUS_TRANSLATE_DOMAIN),
                    'text_not_found' => __('checkout_text_not_found', WCUS_TRANSLATE_DOMAIN),
                    'text_more_chars' => __('checkout_text_more_chars', WCUS_TRANSLATE_DOMAIN)
                ]
            ];

            $globals['i18n'] = apply_filters('wcus_checkout_i18n', $globals['i18n'], $translator->getCurrentLanguage());
        }
        else {
            $globals['i10n'] = [
                'placeholder_area' => $translates['placeholder_area'],
                'placeholder_city' => $translates['placeholder_city'],
                'placeholder_warehouse' => $translates['placeholder_warehouse'],
                'not_found' => $translates['not_found']
            ];
        }

        wp_localize_script('wcus_checkout_js', 'wc_ukr_shipping_globals', $globals);
    }
}