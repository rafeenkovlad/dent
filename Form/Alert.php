<?php
namespace Form\alert;

class Alert
{
    public static function doactionAlert($alert, $name)
    {
        add_action('wp_enqueue_scripts', function() {
            wp_register_style('alert_css', plugins_url('db/Form/exception/src/style.css'));
            wp_enqueue_style('alert_css');
            wp_register_script('alert_js', plugins_url('db/Form/exception/src/script.js'));
            wp_register_script('bootstrap_js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js');
            add_action('wp_footer', function (){
                wp_enqueue_script('min_js');
                wp_enqueue_script('bootstrap_js');
                wp_enqueue_script('alert_js', $deps = array(), $ver = false, $in_footer = true);
            });
        });
        add_action('alert_red', [Alert::class, 'alert'], 20, 2);
        do_action('alert_red', $alert, $name);
    }

    public static function alert($alert, $name)
    {
        return require_once ('exception/src/index.html');
    }
}