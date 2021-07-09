<?php
namespace Myprofile\write;

class Write_profile {

    private static function get_script()
    {
        add_action('wp_enqueue_scripts', function(){
            wp_register_style('reg_form_css', plugins_url('db/Form/profile/src/style.css'));
            wp_enqueue_style('reg_form_css');

            wp_register_style('reg_form_css_bootstrap', plugins_url('db/Form/profile/src/bootstrap.min.css'));
            wp_enqueue_style('reg_form_css_bootstrap');

            wp_register_script('reg_form_prefixfree_js', plugins_url('db/Form/profile/src/prefixfree.min.js'));
            wp_enqueue_script('reg_form_prefixfree_js');
            add_action('wp_footer', function(){
                wp_register_script('min_js', plugins_url('db/Form/js/jquery.min.js'));
                wp_enqueue_script('min_js');

                wp_register_script('reg_form_script_js', plugins_url('db/Form/profile/src/script.js'));
                wp_enqueue_script('reg_form_script_js');

            });

        });
    }

    public static function set_profile_reg()
    {
        add_filter( 'template_include', function(){
            self::get_script();
            wp_head();
            load_template('wp-content/plugins/db/Form/profile/src/index.php');
            wp_footer();
        });
    }
}
