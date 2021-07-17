<?php
namespace Form\profile;

use Filter\profile\Profile_user;

class User_profile_static
{
    private static $profile;
    private static $list;
    private static $id_wp_user, $id_post, $like_sum, $made_in_company, $price, $litle_info, $img_url, $company_id, $user_id;

    public static function user_profile()
    {
        add_action('wp_enqueue_scripts', function(){
                //Открытие картинки в полный размер на странице с товарным листом
                wp_register_style('gods_list_css', plugins_url('db/Form/userprofile/user_profile_static/src/assets_img/css/style.css'));
                wp_enqueue_style('gods_list_css');
                wp_register_script('gods_list_js', plugins_url('db/Form/userprofile/user_profile_static/src/assets_img/intense.js'));
                wp_enqueue_script('gods_list_js');
                //в разработке
                wp_register_style('userprofile_css', plugins_url('db/Form/userprofile/user_profile_static/src/style.css'));
                wp_enqueue_style('userprofile_css');
                wp_register_script('userprofile_js', plugins_url('db/Form/userprofile/user_profile_static/src/script.js'));
                add_action('wp_footer', function(){
                    wp_enqueue_script('userprofile_js');
                });


        });

        add_shortcode('profile_list',[User_profile_static::class, 'get_list']);
    }

    public static function get_list()
    {
        self::$id_post = get_the_ID();
        self::$id_wp_user = get_the_author_meta('ID');
        $profile = Profile_user::getUserProfileObject(self::$id_wp_user);
        self::$profile = $profile[0];
        self::$profile->company_logo = self::$profile->company_logo ?? self::$profile->img;
        $list = Profile_user::getUserListGods(self::$id_wp_user);
        self::$list = $list;
        $like_sum = Profile_user::like_sum_author(self::$id_wp_user);
        self::$like_sum =$like_sum;

        require_once ('userprofile/user_profile_static/src/index.html');

        add_action('profile_list', [User_profile_static::class, 'set_img_upload']);
        do_action('profile_list');
        add_action('del_img_profile', [User_profile_static::class, 'img_del']);
        do_action('del_img_profile');
    }

    private static function imgUrl($img_id,$id)
    {
        self::$user_id = wp_get_current_user()->ID;
        $form = (self::$id_wp_user == self::$user_id)?'
            <form id="form_god_upload" method="post" action="" enctype="multipart/form-data">
                <input type="file" name="img_god_upload" id="img_god_upload" multiple="false" />
                <input type="hidden" name="id_god" value="'.$id.'"  />
                <input type="hidden" name="id_post" value="'.self::$id_post.'"  />'.
                wp_nonce_field('img_god_upload', 'nonce_img_upload').
                '<input type="submit" id="img_god_submit" name="img_god_submit" value="добавить" />
            </form>': NULL;
        $form_del = '<form id="img_delete" method="get" action="">
                       <input type="hidden" name="id_img" value="'.$img_id.'"  />
                       <input type="hidden" name="id_list" value="'.$id.'"  />  
                       <input type="submit" id="img_god_submit" name="img_del_submit" value="удалить" />
                       </form>';
        return (is_null($img_id))? $form : wp_get_attachment_image($img_id, 'list_gods_img', false, array('class' => 'list-gods-img', 'data-image' => wp_get_attachment_url($img_id))).$form_del;

    }

    public static function set_img_upload()
    {
        if(isset($_POST['nonce_img_upload']) && wp_verify_nonce($_POST['nonce_img_upload'], 'img_god_upload') && self::$id_wp_user === self::$user_id)
        {
            switch($_FILES['img_god_upload']['type']):
                case 'image/jpeg':
                    $format = true;
                    break;
                case 'image/gif':
                    $format = true;
                    break;
                case 'image/png':
                    $format = true;
                    break;
                default: $format = false;
            endswitch;
            if($format):
                require_once (ABSPATH . 'wp-admin/includes/image.php');
                require_once (ABSPATH . 'wp-admin/includes/file.php');
                require_once (ABSPATH . 'wp-admin/includes/media.php');
                $attachment_id = media_handle_upload('img_god_upload', $_POST['id_post']);
                //$url = wp_get_attachment_url($attachment_id);
                if(!is_wp_error($attachment_id)){
                    Profile_user::setImgUrlList($_POST['id_god'], $attachment_id);
                }else{var_dump('Ошибка.');}
            endif;
        }
    }

    public static function img_del()
    {
        if(isset($_GET['img_del_submit']) && self::$id_wp_user === self::$user_id)
        {
            wp_delete_attachment( $_GET['id_img'], true);
            Profile_user::del_img_list($_GET['id_list']);
        }
    }

}