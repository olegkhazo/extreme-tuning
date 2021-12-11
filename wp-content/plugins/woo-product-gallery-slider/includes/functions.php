<?php


if (! function_exists('wpgs_get_option')) {
    /**
     * Get Setting option
     *
     * @param [string] $option
     * @param [string] $section
     * @param string $default
     * @return void
     */
    function wpgs_get_option($option, $section, $default = '')
    {
        $options = get_option($section);

        if (isset($options[$option])) {
            return $options[$option];
        }

        return $default;
    }
}
