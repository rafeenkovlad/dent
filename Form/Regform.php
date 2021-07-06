<?php
namespace Form\regform;

class Regform
{
    public static function get_reg_form()
    {
        add_action('wp_enqueue_scripts', function() {
            wp_register_style('reg_css', plugins_url('db/Form/reg/css/style.css'));
            wp_enqueue_style('reg_css');
        });

        add_shortcode('reg_form', array(Regform::class, 'reg_form'));
    }

    public static function reg_form($atts, $content=null)
    {
        $content = require_once('reg/index.html');
        return $content[0];
    }
}

