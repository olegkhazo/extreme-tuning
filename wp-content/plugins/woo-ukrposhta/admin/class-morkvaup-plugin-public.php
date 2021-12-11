<?php class MUP_Plugin_Public
{
    private $plugin_name;
    private $version;
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-morkvaup-plugin-i18n.php';
        add_action('admin_enqueue_styles', array(
            $this,
            'enqueue_styles'
        ));
    }
    public function enqueue_styles()
    {
    }
    public function enqueue_scripts()
    {
    }
}


