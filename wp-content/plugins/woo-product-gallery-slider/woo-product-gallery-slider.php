<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Product Gallery Slider for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/woo-product-gallery-slider/
 * Description:       Customizable image gallery slider for the single product page
 * Version:           2.1.3
 * Author:            codeixer
 * Author URI:        http://codeixer.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpgs
 * Domain Path:       /languages
 * Tested up to: 5.6.1
 * WC requires at least: 3.9
 * WC tested up to: 5.2.2
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
require __DIR__ . '/vendor/autoload.php';


final class Cipg_Slider
{

    /**
     * Plugin version
     * @var string
     */
    const version = '2.1.3';
    
    private function __construct()
    {
        register_activation_hook(__FILE__, array( $this, 'plugin_activation'));
        
        $this->define_constants();
        add_action('woocommerce_loaded', [$this, 'init_plugin'], 30);
        add_filter('plugin_action_links_'. plugin_basename(__FILE__).'', [$this, 'wpgs_plugin_row_meta']);
    }
    /**
     * Add Pro version link into the plugin row meta
     *
     * @param [type] $links
     * @return void
     */
    public function wpgs_plugin_row_meta($links)
    {
        $row_meta = array(
            'settings' => '<a href="'.admin_url('admin.php?page=wcpg-settings').'">Settings</a>',
            'docs' => '<a href="' . esc_url('http://codeixer.com/twist') . '" target="_blank" aria-label="' . esc_attr__('PRO Version', 'wpgs') . '" style="color:green;font-weight:600;">' . esc_html__('Get PRO', 'wpgs') . '</a>',
        );
        return  array_merge($links, $row_meta);
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin()
    {
        new \Prodcut_Gallery_Sldier\Bootstrap;
    }

    /**
     * Run Codes on Plugin activation
     *
     * @return void
     */
    public function plugin_activation()
    {
        $installed = get_option('ciwpgs_installed');

        if (! $installed) {
            update_option('ciwpgs_installed', date("Y/m/d"));
        }
    }
    /**
     * Define the required plugin constants
     * @return void
     */
    public function define_constants()
    {
        define('CIPG_VERSION', self::version);
        define('CIPG_FILE', __FILE__);
        define('CIPG_PATH', __DIR__);
        define('CIPG_URL', plugins_url('', CIPG_FILE));
        define('CIPG_ASSETS', CIPG_URL . '/assets');
    }

    /**
     * Initializes a singleton instance
     *
     * @return $instance
     */
    public static function init()
    {
        static $instance = false;

        if (! $instance) {
            $instance = new self();
        }

        return $instance;
    }
}


// kick-off the plugin
Cipg_Slider::init();
