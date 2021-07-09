<?php
namespace Form\profile;

use Filter\profile\Profile_user;

class User_profile
{
    private static $profile;

    public static function profile($profile)
    {
        self::$profile = $profile[0];
        self::$profile->company_logo = self::$profile->company_logo ?? self::$profile->img;


        add_shortcode('profile_form',[User_profile::class, 'get_form'] );

        add_action('wp_enqueue_scripts', function(){
            if(is_page(149)){
                wp_register_style('userprofile_css', plugins_url('db/Form/userprofile/dist/style.css'));
                wp_enqueue_style('userprofile_css');
                wp_register_script('userprofile_js', plugins_url('db/Form/userprofile/dist/script.js'));
                wp_register_script('userprofile_min_js', plugins_url('db/Form/userprofile/dist/jquery.min.js'));
                wp_register_script('ajax_profile_update', plugins_url('db/Form/userprofile/dist/update.js'));
                wp_localize_script('ajax_profile_update', 'ajaxUpdateProfile', ['url' => get_site_url().'/wp-json/profile/v1/set']);
                add_action('wp_footer', function(){
                    wp_enqueue_script('userprofile_js');
                    wp_enqueue_script('userprofile_min_js');
                    wp_enqueue_script('ajax_profile_update');
                });


            }
        });

    }

    public static function get_form()
    {
       include('userprofile/dist/index.html');
        //Подключаем обработчик формы csv
        $setcsv = new Profile_user(true);
        $setcsv->setListCsv();
    }
}