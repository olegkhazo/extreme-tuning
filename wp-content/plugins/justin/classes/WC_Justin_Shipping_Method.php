<?php

if ( ! defined('ABSPATH') ) {
	exit;
}

class WC_Justin_Shipping_Method extends WC_Shipping_Method
{
  public function __construct($instance_id = 0)
  {
	$this->instance_id = absint( $instance_id );
    parent::__construct($instance_id);
    $this->id = JUSTIN_METHOD_NAME;
    $this->method_title = JUSTIN_METHOD_TITLE;
    $this->method_description = '';

    $this->supports           = array(
	    'shipping-zones',
	    'instance-settings',
	    'instance-settings-modal',
    );

    $this->init();
	// Get setting values
	$this->title = $this->get_option( 'title' );//$this->settings['title'];
	$this->enabled = true;
  }

	public function __get($name) {
		return $this->$name;
	}

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    function init()
    {
      $this->init_settings();
      $this->init_form_fields();

      $translator = new \morkva\JustinShip\classes\NPTranslator();
      $translates = $translator->getTranslates();

	    $this->title = $translates['method_title'];

      // Save settings in admin if you have any defined
      add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

	/**
     * Initialise settings form fields.
     */
    public function init_form_fields()
    {
        $this->instance_form_fields = array(
            'title' => array(
                'title' => __('Заголовок'),
                'type' => 'text',
                'default' => __('Justin до відділення'),
                'placeholder' => __('Justin до відділення')
            ),
            'settings' => array(
                  'title' => __(''),
                  'type' => 'hidden',
                  'description' => __('Решта налаштувань доступні за <a target="_blank" href="admin.php?page=morkvajustin_plugin">посиланям</a>.'),
                  'default' => __(' ')
            )
        );
    }

	/**
     * calculate_shipping function.
     *
     * @access public
     *
     * @param array $package
     */
    public function calculate_shipping($package = array())
    {
      $rate = array(
        'label' => $this->title,
        'cost' => get_option('woo_justin_np_price', 0),
        'package' => $package,
      );
	    $this->add_rate($rate);
    }

    /**
     * Is this method available?
     * @param array $package
     * @return bool
     */
    public function is_available($package)
    {
        return $this->is_enabled();
    }
}
