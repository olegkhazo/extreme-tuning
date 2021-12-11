<?php
/*
  Register all actions and filters for the plugin
*/

require ("class-morkvaup-plugin-callbacks.php");

class MUP_Plugin_Loader
{

    public $tdb = MUP_TABLEDB;
    /**
     * The array of pages for plugin menu
     *
     * @since 1.0.0
     * @access protected
     * @var array $pages 	Pages for plugin menu
     */
    protected $pages;

    /**
     * The array of subpages for plugin menu
     *
     * @since 1.0.0
     * @access protected
     * @var array $subpages 	Subpages for plugin menu
     */
    protected $subpages;

    /**
     * Array of settings groups fields for plugin
     *
     * @since 1.0.0
     * @access protected
     * @var array $settings
     */
    protected $settings;

    /**
     * Array of sections for settings fields for plugin
     *
     * @since 1.0.0
     * @access protected
     * @var array $sections
     */
    protected $sections;

    /**
     * Array of fields for settings fields for plugin
     *
     * @since 1.0.0
     * @access protected
     * @var array $fields
     */
    protected $fields;

    /**
     * The array of actions registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
     */
    protected $actions;
    /**
     * The array of filters registered with WordPress.
     *
     * @since    1.0.0
     * @access   protected
     * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
     */
    protected $filters;

    /**
     * Object of callbacks class
     *
     * @since 	1.0.0
     * @access  protected
     * @var 	string $callbacks 		Class of callbacks
     */
    protected $callbacks;

    /**
     * Initialize the collections used to maintain the actions and filters.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        global $wp_settings_sections;
        $this->actions = array();
        $this->filters = array();
        $this->pages = array();
        $this->subpages = array();
        $this->settings = array();
        $this->sections = array();
        $this->fields = array();

        $this->callbacks = new MUP_Plugin_Callbacks();

        $this->add_settings_fields();
        $this->register_fields_sections();
        $this->register_settings_fields();

        $this->register_menu_pages();
        $this->register_menu_subpages();

        add_action('admin_menu', array(
            $this,
            'register_plugin_menu'
        ));
        add_action('add_meta_boxes', array(
            $this,
            'mv_add_meta_boxes'
        ));
        add_action('admin_init', array(
            $this,
            'register_plugin_settings'
        ));
        add_filter('manage_edit-shop_order_columns', array(
            $this,
            'woo_custom_column'
        ));
        add_action('manage_shop_order_posts_custom_column', array(
            $this,
            'woo_column_get_data'
        ));
        /*add_action('add_meta_boxes', array(
            $this,
            'add_invoice_meta_box'
        ));*/ // Зайвий мета-бокс 'Відправлення Укрпошти' прибраний

        add_filter('wp_mail_from_name', array(
            $this,
            'my_mail_from_name'
        ));
    }
    /**
     * Add a new action to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             The name of the WordPress action that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the action is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
     */

    public function add_action($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->actions = $this->add($this->actions, $hook, $component, $callback, $priority, $accepted_args);
    }
    /**
     * Add a new filter to the collection to be registered with WordPress.
     *
     * @since    1.0.0
     * @param    string               $hook             The name of the WordPress filter that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
     * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
     */
    public function add_filter($hook, $component, $callback, $priority = 10, $accepted_args = 1)
    {
        $this->filters = $this->add($this->filters, $hook, $component, $callback, $priority, $accepted_args);
    }
    /**
     * A utility function that is used to register the actions and hooks into a single
     * collection.
     *
     * @since    1.0.0
     * @access   private
     * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
     * @param    string               $hook             The name of the WordPress filter that is being registered.
     * @param    object               $component        A reference to the instance of the object on which the filter is defined.
     * @param    string               $callback         The name of the function definition on the $component.
     * @param    int                  $priority         The priority at which the function should be fired.
     * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
     * @return   array                                  The collection of actions and filters registered with WordPress.
     */
    private function add($hooks, $hook, $component, $callback, $priority, $accepted_args)
    {
        $hooks[] = array(
            'hook' => $hook,
            'component' => $component,
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args
        );
        return $hooks;
    }
    /**
     * Register the filters and actions with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        foreach ($this->filters as $hook)
        {
            add_filter($hook['hook'], array(
                $hook['component'],
                $hook['callback']
            ) , $hook['priority'], $hook['accepted_args']);
        }
        foreach ($this->actions as $hook)
        {
            add_action($hook['hook'], array(
                $hook['component'],
                $hook['callback']
            ) , $hook['priority'], $hook['accepted_args']);
        }
    }

    /**
     * Registering plugin pages to menu
     *
     * @since 	1.0.0
     */
    public function register_menu_pages()
    {
        $this->pages = array(
            array(
                'page_title' => MUP_PLUGIN_NAME,
                'menu_title' => 'UkrPoshta ',
                'capability' => 'manage_woocommerce',
                'menu_slug' => 'morkvaup_plugin',
                'callback' => array(
                    $this,
                    'add_settings_page'
                ) ,
                'icon_url' => MUP_PLUGIN_URL . "/image/menu-icon.png",
                'position' => 60
            )
        );

        return $this;
    }

    /**
     *	Add Plugin Settings page
     *
     *	@since 	1.0.0
     */
    public function add_settings_page()
    {

        require_once (MUP_PLUGIN_PATH . '/admin/partials/morkvaup-plugin-settings.php');
    }

    /**
     * Registering subpages for menu of plugin
     *
     * @since 	1.0.0
     */
    public function register_menu_subpages()
    {
        $title = "Налаштування";

        $this->subpages = array(
            array(
                'parent_slug' => 'morkvaup_plugin',
                'page_title' => 'Налаштування',
                'menu_title' => 'Налаштування',
                'capability' => 'manage_woocommerce',
                'menu_slug' => 'morkvaup_plugin',
                'callback' => array(
                    $this,
                    'add_settings_page'
                )
            ) ,
            array(
                'parent_slug' => 'morkvaup_plugin',
                'page_title' => 'Створити відправлення',
                'menu_title' => 'Створити відправлення',
                'capability' => 'manage_woocommerce',
                'menu_slug' => 'morkvaup_invoice',
                'callback' => array(
                    $this,
                    'add_invoice_page'
                )
            ) ,
            array(
                'parent_slug' => 'morkvaup_plugin',
                'page_title' => 'Мої відправлення',
                'menu_title' => 'Мої відправлення',
                'capability' => 'manage_woocommerce',
                'menu_slug' => 'morkvaup_invoices',
                'callback' => array(
                    $this,
                    'invoices_page'
                )
            ) ,
            array(
                'parent_slug' => 'morkvaup_plugin',
                'page_title' => 'Про плагін',
                'menu_title' => 'Про плагін',
                'capability' => 'manage_woocommerce',
                'menu_slug' => 'morkvaup_about',
                'callback' => array(
                    $this,
                    'about_page'
                )
            )
        );

        return $this;
    }

    /**
     * Adding subpage of plugin
     *
     * @since 1.0.0
     */
    public function add_invoice_page()
    {
        require_once (MUP_PLUGIN_PATH . 'admin/partials/morkvaup-plugin-form.php');
    }

    /**
     * Add invoices subpage of plugin
     *
     * @since 1.0.0
     */
    public function invoices_page()
    {
        $path = MUP_PLUGIN_PATH . 'admin/partials/morkvaup-plugin-invoices-page.php';
        if (file_exists($path))
        {
            require_once ($path);
        }
        else
        {
            $path = MUP_PLUGIN_PATH . 'admin/partials/morkvaup-plugin-invoices-page-demo.php';
            require_once ($path);

        }
    }

    /**
     * Add about page of plugin
     *
     * @since 1.0.0
     */
    public function about_page()
    {
        //echo file_get_contents( MUP_PLUGIN_PATH . 'admin/partials/morkvaup-plugin-about-page.php');
        require_once (MUP_PLUGIN_PATH . 'admin/partials/morkvaup-plugin-about-page.php');
    }

    /**
     * Register plugin menu
     *
     * @since 	1.0.0
     */
    public function register_plugin_menu()
    {
        foreach ($this->pages as $page)
        {
            add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position']);
        }

        foreach ($this->subpages as $subpage)
        {
            add_submenu_page($subpage['parent_slug'], $subpage['page_title'], $subpage['menu_title'], $subpage['capability'], $subpage['menu_slug'], $subpage['callback']);
        }
    }

    /**
     * Add setting fields for plugin
     *
     * @since 	1.0.0
     */
    public function add_settings_fields()
    {
        $args = array(

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'zone_ukrposhta'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'production_bearer_ecom'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'production_bearer_status_tracking'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'production_cp_token'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'proptype'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'other_settings'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'sendtype'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'senduptype'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'sendwtype'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'title_sender'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'up_company_name'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'title_international'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'up_sender_type'
            ),

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'names1'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'nameslatin'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'streetlatin'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'numlatin'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'citylatin'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'names2'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'names3'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'activate_plugin_en'
            ) ,


            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'ukrposhta_calculate_rates'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'morkva_ukrposhta_default_price'
            ) ,

            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'phone'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'edrpou'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'up_tin'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'flat'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'warehouse'
            ) ,
            array(
                'option_group' => 'morkvaup_options_group',
                'option_name' => 'up_invoice_description'
            ) ,
            /*	array(
            'option_group' => 'morkvaup_options_group',
            'option_name' => 'morkvaup_email_template'
            ),
            array(
            'option_group' => 'morkvaup_options_group',
            'option_name' => 'morkvaup_email_subject'
            )
            */
        );

        $this->settings = $args;

        return $this;
    }

    /**
     *	Register all sections for settings fields
     *
     *	@since 	 1.0.0
     */
    public function register_fields_sections()
    {
        $args = array(
            array(
                'id' => 'morkvaup_admin_index',
                'title' => 'Налаштування',
                'callback' => function ()
                {
                    echo "";
                }
                ,
                'page' => 'morkvaup_plugin'
            )
        );

        $this->sections = $args;

        return $this;
    }

    /**
     * Register settings callbacks fields
     *
     * @since 	1.0.0
     */
    public function register_settings_fields()
    {
        $args = array(

            // Налаштування
            array(
                'id' => 'production_bearer_ecom',
                'title' => 'PROD BEARER eCom',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupAuthBearer'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'production_bearer_ecom'
                )
            ) ,
            array(
                'id' => 'production_bearer_status_tracking',
                'title' => 'PROD BEARER',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupProdBearer'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'production_bearer_status_tracking'
                )
            ) ,
            array(
                'id' => 'production_cp_token',
                'title' => 'PROD COUNTERPARTY TOKEN',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupCpToken'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'production_cp_token'
                )
            ) ,
            // array(
            //     'id' => 'sendwtype',
            //     'title' => 'Тип доставки',
            //     'callback' => array(
            //         $this->callbacks,
            //         'morkvaupsendwtype'
            //     ) ,
            //     'page' => 'morkvaup_plugin',
            //     'section' => 'morkvaup_admin_index',
            //     'args' => array(
            //         'label_for' => 'sendwtype'
            //     )
            // ) ,
            array(
                'id' => 'sendtype',
                'title' => 'Тип відправлення',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupsendtype'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'sendtype'
                )
            ) ,
            array(
                'id' => 'proptype',
                'title' => 'Формат наклейки',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupprinttype'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'proptype'
                )
            ) ,
            array(
                'id' => 'invoice_description',
                'title' => 'Опис відправлення по замовчуванню',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupInvoiceDescription'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'up_invoice_description',
                    'class' => 'up_invoice_description'
                )
            ) ,
            array(
                'id' => 'shipping_costing_announcement',
                'title' => '<span>Розрахунок вартості доставки</span>',
                'callback' => function () {
                    echo '<p>Вартість доставки розраховується автоматично з даних про вагу і розмір товару.</p>';
                },
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
            ) ,
            array(
                'id' => 'morkva_ukrposhta_default_price',
                'title' => 'Ціна доставки',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupmorkva_ukrposhta_default_price'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'morkva_ukrposhta_default_price'
                )
            ) ,

            // Інші налаштування
            array(
                'id' => 'other_settings',
                'title' => '<h3 style="margin-bottom:0;">Інші налаштування</h3>',
                'callback' => function () {},
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
            ) ,
            array(
                'id' => 'ukrposhta_calculate_rates',
                'title' => 'Додавати вартість доставки до суми замовлення?',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupukrposhta_calculate_rates'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'ukrposhta_calculate_rates'
                )
            ) ,
            array(
                'id' => 'zone_ukrposhta',
                'title' => 'Працювати із зонами доставки?',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupzone'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'zone_ukrposhta',
                    'class' => 'basesettings allsettings show'
                )
            ) ,
            array(
                'id' => 'activate_plugin_en',
                'title' => 'Зробити створення ЕН доступним для замовлень з іншими методами доставки?',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupActivateen'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'activate_plugin_en'
                )
            ) ,

            // Відправник
            array(
                'id' => 'title_sender',
                'title' => '<h3 style="margin-bottom:0;">Відправник</h3>',
                'callback' => function () {},
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
            ) ,
            array(
                'id' => 'up_sender_type',
                'title' => __( 'Відправник представляє', 'woo-ukrposhta-pro' ),
                'callback' => array( $this->callbacks, 'morkvaup_sender_type' ),
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'up_sender_type'
                )
            ),
            array(
                'id' => 'up_company_name',
                'title' => 'Назва компанії / ФОП',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupCompanyName'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'up_company_name',
                    'class' => 'up_company_name display_none'
                )
            ) ,
            array(
                'id' => 'edrpou',
                'title' => 'ЄДРПОУ',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupEdrpou'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'edrpou',
                    'class' => 'edrpou display_none'
                )
            ) ,
            array(
                'id' => 'up_tin',
                'title' => 'Індивідуальний податковий номер (ІПН)',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupTin'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'up_tin',
                    'class' => 'up_tin display_none'
                )
            ) ,
            array(
                'id' => 'names1',
                'title' => 'Прізвище',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupNames'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'names1',
                    'class' => 'names1 display_none'
                )
            ) ,
            array(
                'id' => 'names2',
                'title' => 'Ім\'я',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupNames2'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'names2',
                    'class' => 'names2 display_none'
                )
            ) ,

            array(
                'id' => 'names3',
                'title' => 'По-батькові',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupNames3'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'names3',
                    'class' => 'names3 display_none'
                )
            ) ,
            array(
                'id' => 'phone',
                'title' => 'Номер телефону',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupPhone'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'phone',
                    'class' => 'phone display_none'
                )
            ) ,
            array(
                'id' => 'warehouse',
                'title' => 'Поштовий індекс',
                'callback' => array(
                    $this->callbacks,
                    'morkvaupWarehouseAddress'
                ) ,
                'page' => 'morkvaup_plugin',
                'section' => 'morkvaup_admin_index',
                'args' => array(
                    'label_for' => 'warehouse',
                    'class' => 'warehouse'
                )
            ) ,


        );

        $this->fields = $args;

        return $this;
    }

    /**
     *	Registering all settings fields for plugin
     *
     *	@since 	 1.0.0
     */
    public function register_plugin_settings()
    {
        foreach ($this->settings as $setting)
        {
            register_setting($setting["option_group"], $setting["option_name"], (isset($setting["callback"]) ? $setting["callback"] : ''));
        }

        foreach ($this->sections as $section)
        {
            add_settings_section($section["id"], $section["title"], (isset($section["callback"]) ? $section["callback"] : '') , $section["page"]);
        }

        foreach ($this->fields as $field)
        {
            add_settings_field($field["id"], $field["title"], (isset($field["callback"]) ? $field["callback"] : '') , $field["page"], $field["section"], (isset($field["args"]) ? $field["args"] : ''));
        }
    }

    /**
     * Add meta box to WooCommerce order's page
     *
     * @since 1.0.0
     */
    public function add_plugin_meta_box()
    {
        if (!isset($_SESSION))
        {
            session_start();
        }

        if (isset($_GET["post"]))
        {
            $order_id = $_GET["post"];

            $order_data0 = wc_get_order($order_id);
            $order_data = $order_data0->get_data();

            $methodid = '';

            foreach ($order_data0->get_items('shipping') as $item_id => $shipping_item_obj)
            {
                $shipping_item_data = $shipping_item_obj->get_data();
                $methodid = $shipping_item_data['method_id'];
            }
            //echo $methodid;
            if ((strpos($methodid, 'u_poshta_shipping_method') !== false) || (strpos($methodid, 'ukrposhta_shippping') !== false))
            {
                echo '<style>#mvup_other_fields{display:block;}</style>';
            }
            else
            {
                if (!get_option('activate_plugin_en'))
                {
                    echo '<style>#mvup_other_fields{display:none;}</style>';
                }
            }

            if (isset($order_id))
            {
                $order_data = wc_get_order($order_id);
                $order = $order_data->get_data();
                $_SESSION['order_data'] = $order;
                $_SESSION['order_id'] = $order_id;
            }

            echo "<img src='" . MUP_PLUGIN_URL . "/includes/icon.svg' style='width: 20px;margin-right: 20px;'/>";
            echo "<a class='button button-primary send' href='admin.php?page=morkvaup_invoice'>Нове відправлення</a>";
            echo "<script src=" . MUP_PLUGIN_URL . 'admin/js/script.js' . "></script>";
            echo "<link href=" . MUP_PLUGIN_URL . 'admin/css/style.css' . "/>";
            $this->invoice_meta_box_info();
        }
        else
        {
            if (!get_option('activate_plugin_en'))
            {
                echo '<style>#mvup_other_fields{display:none;}</style>';
            }
        }
    }

    /**
     * Generating meta box
     *
     * @since 1.0.0
     */
    public function mv_add_meta_boxes()
    {
        add_meta_box('mvup_other_fields', __('Відправлення Укрпошти', 'woocommerce') , array(
            $this,
            'add_plugin_meta_box'
        ) , 'shop_order', 'side', 'core');
    }

    /**
     * Creating custom column at woocommerce order page
     *
     * @since 1.1.0
     */
    public function woo_custom_column($columns)
    {
        $columns['created_invoice'] = 'Відправлення';
        $columns['invoice_number'] = 'Номер Відправлення';
        return $columns;
    }

    /**
     * Getting data of order column at order page
     *
     * @since 1.1.0
     */
    public function woo_column_get_data($column)
    {
        global $post;
        $tdb = MUP_TABLEDB;
        $data = get_post_meta($post->ID);

        if ($column == 'created_invoice')
        {
            global $wpdb;

            $order_id = $post->ID;
            $results = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}{$tdb} WHERE order_id = '$order_id'", ARRAY_A);

            if (!empty($results))
            {
                $img = "/logo1.svg";
                echo '<img height=25 src="' . site_url() . '/wp-content/plugins/' . plugin_basename(__DIR__) . $img . '" />';
            }
            else
            {
                $img = '/logo2.svg';
                echo '<img height=25 src="' . site_url() . '/wp-content/plugins/' . plugin_basename(__DIR__) . $img . '" />';
            }
        }

        if ($column == 'invoice_number')
        {
            global $wpdb;

            $order_id = $post->ID;
            $query = "SELECT * FROM {$wpdb->prefix}" . $tdb . " WHERE order_id = '$order_id'";
            $number_result = $wpdb->get_row($query, ARRAY_A);

            if ($number_result)
            {
                echo $number_result["order_invoice"];
            }
            else
            {
                echo "";
            }
        }
    }

    /**
     * Add meta box with invoice information
     *
     * @since 1.1.0
     */
    /*public function add_invoice_meta_box()
    {
        if (isset($_GET["post"]))
        {
            add_meta_box('up_invoice_other_fields', __('Відправлення Укрпошти', 'woocommerce') , array(
                $this,
                'invoice_meta_box_info'
            ) , 'shop_order', 'side', 'core');
        }

    }*/ // Зайвий мета-бокс 'Відправлення Укрпошти' прибраний

    /**
     * Add info of invoice meta box
     *
     * @since 1.1.0
     */
    public function invoice_meta_box_info()
    {
        $tdb = MUP_TABLEDB;

        if (isset($_GET["post"]))
        {
            $order_id = $_GET["post"];
        }

        $selected_order = wc_get_order($order_id);

        $order = $selected_order->get_data();
        $meta_ttn = get_post_meta($order_id, 'ukrposhta_ttn', true);

        if (empty($meta_ttn)) {
            global $wpdb;
            $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}{$tdb} WHERE order_id = '$order_id'", ARRAY_A);
            if (isset($result[0]['order_invoice'])) {
                $meta_ttn = $result[0]['order_invoice'];
            }
        }
        $invoice_email = $order['billing']['email'];

        if ( ! empty( $meta_ttn ) ) {
            $invoice_number = $meta_ttn;
            echo '<div style="margin-top:10px;">Номер Відправлення: ' . $meta_ttn . '</div>';
            // getting ukrposhta credentials
            $bearer = get_option( 'production_bearer_ecom' );
            $cptoken = get_option('production_cp_token');
            $tbearer = get_option('production_bearer_status_tracking');

            if (!class_exists( 'UkrposhtaApi')) {
                require MORKVA_UKRPOSHTA_PLUGIN_DIR . 'admin/partials/api.php';
            }
            //set up new ukrposhta apiobject
            $ukrposhtaApi = new UkrposhtaApi($bearer ,$cptoken, $tbearer);

            $invoiceType = $ukrposhtaApi->GetInfo( $invoice_number );
            $invoiceRef = $invoiceType['uuid'];
            if ( $invoiceType['type'] != "INTERNATIONAL" ) {
                // create button in meta-box 'mvup_other_fields' to print ukrposhta invoice sticker
                echo '<div></form><form target="_blank" action="' . dirname( plugin_dir_url( __FILE__ ) ) . '/admin/partials/pdf.pdf' . '" method="POST" />';
                echo '<input type="text" name="type" value="' . get_option( 'proptype' ) . '" style="display:none;" />
                        <input class="startcodeup" type="text" name="ttn" value="' . $invoiceRef . '" hidden />
                        <input type="text" name="bearer" value="' . $bearer . '" hidden />
                        <input tyoe="text" name="cp_token" value="' . $cptoken . '" hidden />';
                echo '<a style="margin: 5px;" alert="У новій вкладці відкриється документ для друку" title="Друк адресного ярлика" class="formsubmitup button" />' . ' <img src="' . plugins_url('img/003-barcode.png', __FILE__) . '" style="vertical-align:text-bottom;margin-right:5px;" /> Друк стікера </a></div>';
                echo '</form><form>';
            }

            $methodProperties = array(
                "Documents" => array(
                    array(
                        "DocumentNumber" => $invoice_number
                    ) ,
                )
            );
        }
        else
        {
            echo '<div style="margin-top:10px;">Номер відправлення не встановлено: -</div>';
        }

    }

    /**
     * From name email
     *
     * @since 1.1.3
     */
    public function my_mail_from_name($name)
    {
        return get_option('blogname');
    }


    /**
     * Update plugin of MORKVA
     *
     * @return void
     */
    public function morkvaup_update_plugin($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        // trying to get from cache first, to disable cache comment 10,20,21,22,24
        if (false == $remote = get_transient('morkva_update_morkvaup-plugin')) {

        // info.json is the file with the actual plugin information on your server
        /*$remote = wp_remote_get( 'https://YOUR_WEBSITE/SOME_PATH/info.json', array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/json'
            ) )
        );

        if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
            set_transient( 'morkva_upgrade_morkvaup-plugin', $remote, 43200 ); // 12 hours cache
        }*/
        }

        $remote = json_encode(array(
        "name" => "Nova Poshta TTN Pro",
        "slug" => "nova-poshta-ttn-pro",
        "version" => "1.2.2",
        "requires" => "7.3",
        "author" => "<a href='https://morkva.co.ua'>MORKVA</a>",
        "author_profile" => "https://morkva.co.ua",
        "download_url"=> "https://wordpress.org/plugins/nova-poshta-ttn/",
        "sections" => array(
            "description" => "Плагін допомагає автоматизувати процес відправки ваших замовлень через Нову Пошту. На сторінці замовлення можна згенерувати відправлення із даних, які вносив покупець при оформленні. Ви просто приходите на відділення і кажете номер відправлення менеджеру."
        )
    ));

        if ($remote) {
            $remote = json_decode($remote['body']);

            // your installed plugin version should be on the line below! You can obtain it dynamically of course
            if ($remote && version_compare('1.0', $remote->version, '<') && version_compare($remote->requires, get_bloginfo('version'), '<')) {
                $res = new stdClass();
                $res->slug = 'morkvaup-plugin';
                $res->plugin = 'nova-poshta-ttn-pro/morkvaup-plugin.php'; // it could be just YOUR_PLUGIN_SLUG.php if your plugin doesn't have its own directory
                $res->new_version = $remote->version;
                // $res->tested = $remote->tested;
                $res->package = $remote->download_url;
                // $res->url = $remote->homepage;
                $transient->response[$res->plugin] = $res;
                //$transient->checked[$res->plugin] = $remote->version;
            }
        }

        return $transient;
    }
}
