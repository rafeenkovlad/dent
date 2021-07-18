<?php
namespace Form\profile;

class Write_new_post
{

    public static function new_post_profile()
    {
        add_action('wp_enqueue_scripts', function(){
            //Открытие картинки в полный размер на странице с товарным листом
            wp_register_style('newpost_css', plugins_url('db/Form/newpost/src/style.css'));
            wp_enqueue_style('newpost_css');
            wp_register_script('header_js', plugins_url('db/Form/newpost/src/header_script.js'));
            wp_enqueue_script('header_js');

            wp_register_script('footer_js', plugins_url('db/Form/newpost/src/footer_script.js'));
            add_action('wp_footer', function(){
                wp_enqueue_script('footer_js');
            });
        });

        add_shortcode('new_post', [Write_new_post::class, 'element_new_post']);
    }

    public static function element_new_post()
    {
        (get_current_user_id())?require_once ('newpost/src/index.html'):print_r('Необходимо авторизваться.');
    }

    private static function editor()
    {
        return  wp_editor($content, 'mycustomeditor',
            [
                'wpautop' => 1,
                'media_buttons' => 1,
                'textarea_name' => 'textarea_new_post', //нужно указывать!
                'textarea_rows' => 20,
                'tabindex' => null,
                'editor_css' => '',
                'editor_class' => '',
                'teeny' => 0,
                'dfw' => 0,
                'tinymce' => 1,
                'quicktags' => 1,
                'drag_drop_upload' => true
            ]);
    }

}