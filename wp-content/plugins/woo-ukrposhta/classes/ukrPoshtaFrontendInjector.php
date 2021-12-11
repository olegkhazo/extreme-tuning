<?php

namespace deliveryplugin\Ukrposhta\classes;


if ( ! defined('ABSPATH')) {
  exit;
}

class ukrPoshtaFrontendInjector
{
  /**
   * @var UPTranslator
   */
  private $translator;

  public function __construct()
  {
    $this->translator = new UPTranslator();

    add_action('wp_head', [ $this, 'injectGlobals' ]);
    add_action('wp_enqueue_scripts', [ $this, 'injectScripts' ]);
    add_action($this->getInjectActionName(), [ $this, 'injectShippingFields' ]);

    // Prevent default WooCommerce rate caching
    /*add_filter('woocommerce_shipping_rate_label', function ($label, $rate) {
      if ($rate->get_method_id() === 'ukrposhta_shippping') {
        $label = __('Укрпошта', 'woo-ukrposhta-pro');
      }

      return $label;
    }, 10, 2);*/
  }

  public function injectGlobals()
  {
    if ( ! is_checkout()) {
      return;
    }

    ?>
    <style>
      .wc-ukrposhta-up-fields {
        padding: 1px 0;
      }

      .wcus-state-loading:after {
        border-color: <?= get_option('morkva_ukrposhta_spinner_color', '#dddddd'); ?>;
        border-left-color: #fff;
      }
    </style>
  <?php
  }

  public function injectScripts()
  {
	  if ( ! is_checkout()) {
		  return;
	  }

    wp_enqueue_style(
      'morkva_ukrposhta_css',
      MORKVA_UKRPOSHTA_PLUGIN_URL . 'assets/css/style.min.css', null, MUP_PLUGIN_VERSION
    );

    wp_enqueue_script(
      'morkva_ukrposhta_ukr_poshta_checkout',
      MORKVA_UKRPOSHTA_PLUGIN_URL . 'assets/js/checkout-up.js',
      [ 'jquery' ],
      MUP_PLUGIN_VERSION,
      true
    );

    $translator = new UPTranslator();
    $translates = $translator->getTranslates();

        wp_localize_script('morkva_ukrposhta_ukr_poshta_checkout', 'morkva_ukrposhta_globals', [
          'ajaxUrl'                     => admin_url('admin-ajax.php'),
          'homeUrl'                     => home_url(),
          'lang'                        => apply_filters('morkva_ukrposhta_language', get_option('morkva_ukrposhta_up_lang', 'ru')),
          'disableDefaultBillingFields' => apply_filters('morkva_ukrposhta_prevent_disable_default_fields', false) === false ?
            'true' :
            'false',
          'i10n' => [
            'placeholder_area' => $translates['placeholder_area'],
            'placeholder_city' => $translates['placeholder_city'],
            'placeholder_warehouse' => $translates['placeholder_warehouse']
          ]
        ]);

  }

  public function injectShippingFields()
  {
	  if ( ! is_checkout()) {
		  return;
	  }

	  $translates = $this->translator->getTranslates();

    ?>
      <div id="<?= MORKVA_UKRPOSHTA_UP_SHIPPING_NAME; ?>_fields" class="wc-ukrposhta-up-fields">
        <h3><?= $translates['block_title']; ?></h3>
        <div id="nova-poshta-shipping-info">
          <?php

          //Warehouse
          woocommerce_form_field(MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_warehouse', [
            'type' => 'text',
            'required'  => 'true',
            'input_class' => [
              'wc-ukrposhta-select'
            ],
            'label' => esc_html__('Індекс Відділення', 'woo-ukrposhta-pro'),
            'placeholder' => 'Введіть поштовий індекс отримувача',
          ]);          

          //City
          woocommerce_form_field(MORKVA_UKRPOSHTA_UP_SHIPPING_NAME . '_city', [
            'type' => 'text',
            'required'  => 'true',
            'input_class' => [
              'wc-ukrposhta-select'
            ],
            'label' => esc_html__('Населений пункт', 'woo-ukrposhta-pro'),
            'placeholder' => 'Введіть назву міста отримувача',
          ]);

          ?>
        </div>
      </div>
    <?php
  }



  private function getCitySelectAttributes($placeholder)
  {
    return [
   'options' => "",
   'default' => $placeholder
 ];
  }

  private function getWarehouseSelectAttributes($placeholder)
  {
      return [
     'options' => "",
     'default' => $placeholder
   ];
  }

  private function getInjectActionName()
  {
    return 'additional' === morkva_ukrposhta_get_option('morkva_ukrposhta_up_block_pos')
      ? 'woocommerce_before_order_notes'
      : 'woocommerce_after_checkout_billing_form';
  }
}
