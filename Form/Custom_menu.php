<?php
namespace Form;

class Custom_menu
{
    public static function connect_menu()
    {
        add_action('wp_enqueue_scripts', function(){

            wp_register_style('custom_menu_css', plugins_url('db/Form/custommenu/style.css'));
            wp_enqueue_style('custom_menu_css');

            wp_register_script('custom_menu_js', plugins_url('db/Form/custommenu/script.js'));

            add_filter('wp_footer', function(){
                wp_enqueue_script('custom_menu_js');
            });


        });

        add_shortcode('get_dental_menu', [Custom_menu::class, 'get_html_menu']);
    }

    public static function get_html_menu()
    {
        require_once ('custommenu/index.html');
    }
}