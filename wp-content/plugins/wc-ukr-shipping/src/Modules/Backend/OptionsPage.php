<?php

namespace kirillbdev\WCUkrShipping\Modules\Backend;

use kirillbdev\WCUkrShipping\Foundation\State;
use kirillbdev\WCUkrShipping\Http\Controllers\AddressBookController;
use kirillbdev\WCUkrShipping\Http\Controllers\OptionsController;
use kirillbdev\WCUkrShipping\States\WarehouseLoaderState;
use kirillbdev\WCUSCore\Contracts\ModuleInterface;
use kirillbdev\WCUSCore\Foundation\View;
use kirillbdev\WCUSCore\Http\Routing\Route;

if ( ! defined('ABSPATH')) {
    exit;
}

class OptionsPage implements ModuleInterface
{
    public function init()
    {
        add_action('admin_menu', [$this, 'registerOptionsPage'], 99);
    }

    public function routes()
    {
        return [
            new Route('wcus_save_options', OptionsController::class, 'save'),
            new Route('wcus_load_areas', AddressBookController::class, 'loadAreas'),
            new Route('wcus_load_cities', AddressBookController::class, 'loadCities'),
            new Route('wcus_load_warehouses', AddressBookController::class, 'loadWarehouses')
        ];
    }

    public function registerOptionsPage()
    {
        State::add('warehouse_loader', WarehouseLoaderState::class);

        add_menu_page(
            __('options_page_title', WCUS_TRANSLATE_DOMAIN),
            'WC Ukr Shipping',
            'manage_options',
            'wc_ukr_shipping_options',
            [$this, 'html'],
            WC_UKR_SHIPPING_PLUGIN_URL . 'image/menu-icon.png',
            '56.15'
        );
    }

    public function html()
    {
        echo View::render('settings');
    }

    public function premiumHtml()
    {
        wp_redirect('https://kirillbdev.pro/wc-ukr-shipping-pro/?ref=plugin_menu', 301);
        exit;
    }
}