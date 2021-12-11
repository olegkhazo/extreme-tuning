<?php
namespace Prodcut_Gallery_Sldier;

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
if (!class_exists('Settings')):
class Settings
{
    private $settings_api;

    public function __construct()
    {
        $this->settings_api = new \WeDevs_Settings_API;

        add_action('admin_init', array($this, 'admin_init'));
        add_action('codeixer_sub_menu', array($this, 'sub_menu_page'));
    }

    public function sub_menu_page()
    {
        add_submenu_page('codeixer', 'Gallery Settings', 'Gallery Settings', 'manage_options', 'wcpg-settings', array( $this, 'plugin_page' ));
    }

    public function admin_init()
    {

        //set the settings
        $this->settings_api->set_sections($this->get_settings_sections());
        $this->settings_api->set_fields($this->get_settings_fields());

        //initialize settings
        $this->settings_api->admin_init();
    }

    public function get_settings_sections()
    {
        $sections = array(
            array(
                'id'    => 'wpgs_settings',
                'title' => __('General Options', 'wpgs')
            ),
            array(
                'id'    => 'wpgs_lightbox_settings',
                'title' => __('Lightbox Options', 'wpgs')
            ),
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    public function get_settings_fields()
    {
        $settings_fields = array(
                'wpgs_settings'   => array(
                    array(
                        'name'    => 'navIcon',
                        'label'   => __('Navigation Icons', 'wpgs'),
                        'desc'    => __('Show Navigation icons. Default: Yes', 'wpgs'),
                        'type'    => 'select',
                        'default' => 'true',
                        'options' => array(
                            'true' => 'Yes',
                            'false'  => 'No',
                        ),
                    ),
                    array(
                        'name'    => 'navColor',
                        'label'   => __('Icon Color', 'wpgs'),
                        'desc'    => __('', 'wpgs'),
                        'type'    => 'color',
                        'default' => '',
                    ),
                     array(
                        'name'              => 'thubms',
                        'label'             => __('Thumbnails to Show', 'wpgs'),
                        'desc'              => __('Default: 4', 'wpgs'),

                        'type'              => 'number',
                        'default'           => '4',
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    array(
                        'name'    => 'autoPlay',
                        'label'   => __('Slider AutoPlay', 'wpgs'),
                        'desc'    => __('Default: No', 'wpgs'),
                        'type'    => 'select',
                        'default' => 'false',
                        'options' => array(
                            'true' => 'Yes',
                            'false'  => 'No',
                        ),
                    ),
                    array(
                        'name'    => 'infinity_loop',
                        'label'   => __('Slide Infinitely', 'wpgs'),
                        'desc'    => __('Sliding Infinite Loop. Default: No', 'wpgs'),
                        'type'    => 'select',
                        'default' => 'false',
                        'options' => array(
                            'true' => 'Yes',
                            'false'  => 'No',
                        ),
                    ),
                    array(
                        'name'    => 'mouse_draging',
                        'label'   => __('Mouse Dragging', 'wpgs'),
                        'desc'    => __('Move image on Mouse dragging. Default: No', 'wpgs'),
                        'type'    => 'select',
                        'default' => 'false',
                        'options' => array(
                            'true' => 'Yes',
                            'false'  => 'No',
                        ),
                    ),
                    array(
                        'name'    => 'zoom',
                        'label'   => __('Image Zoom', 'wpgs'),
                        'desc'    => __('Enable/disable WooCommerce default zoom feature. Default: No', 'wpgs'),
                        'type'    => 'select',
                        'default' => 'false',
                        'options' => array(
                            'true' => 'Yes',
                            'false'  => 'No',
                        ),
                    ),
                     
                    
                ),
                'wpgs_lightbox_settings'   => array(
                    array(
                        'name'    => 'caption',
                        'label'   => __('Image Caption', 'wpgs'),
                        'desc'    => __('Show Image Attributes as caption in this Lightbox', 'wpgs'),
                        'type'    => 'select',
                        'default' => 'false',
                        'options' => array(
                            'true' => 'Yes',
                            'false'  => 'No',
                        ),
                    ),
                    array(
                        'name'    => 'bg_color',
                        'label'   => __('Background Color', 'wpgs'),
                        'desc'    => __('set lightbox background color', 'wpgs'),
                        'type'    => 'color',
                        'default' => '#000',
                    ),
                   
                    
                )
            );

        return $settings_fields;
    }

    public function plugin_page()
    {
        echo '<div class="wrap">';
        echo '<h1>Product Gallery Slider Settings</h2>';
        do_action('wcpg_admin_top');
        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();
        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    public function get_pages()
    {
        $pages = get_pages();
        $pages_options = array();
        if ($pages) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }
}
endif;
