<?php
namespace Form\profile;

use Filter\profile\Profile_user;
use Route\Wp\{SetImgList,DelImgList};

class User_profile_static
{
    private static $profile;
    private static $list;
    public static $id_wp_user, $id_post, $like_sum, $made_in_company, $price, $litle_info, $img_url, $company_id, $user_id;

    public static function user_profile()
    {
        add_action('wp_enqueue_scripts', function(){
                //Открытие картинки в полный размер на странице с товарным листом
                wp_register_style('gods_list_css', plugins_url('db/Form/userprofile/user_profile_static/src/assets_img/css/style.css'));
                wp_enqueue_style('gods_list_css');
                wp_register_style('bootstrap_css', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
                wp_enqueue_style('bootstrap_css');
                wp_register_script('gods_list_js', plugins_url('db/Form/userprofile/user_profile_static/src/assets_img/intense.js'));
                wp_enqueue_script('gods_list_js');
                //в разработке
                wp_register_style('userprofile_css', plugins_url('db/Form/userprofile/user_profile_static/src/style.css'));
                wp_enqueue_style('userprofile_css');
                wp_register_script('userprofile_js', plugins_url('db/Form/userprofile/user_profile_static/src/script.js'));
                wp_register_script('min_js', plugins_url('db/Form/js/jquery.min.js'));
                add_action('wp_footer', function(){
                    wp_enqueue_script('userprofile_js');
                    wp_enqueue_script('min_js');
                });


        });

        add_shortcode('profile_list',[User_profile_static::class, 'get_list']);
        add_action('register_route_set_img', [SetImgList::class, 'register_route']);
        do_action('register_route_set_img');
        add_action('register_route_del_img', [DelImgList::class, 'register_route']);
        do_action('register_route_del_img');
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

        //add_action('profile_list', [User_profile_static::class, 'set_img_upload']);
        //do_action('profile_list');
        //add_action('del_img_profile', [User_profile_static::class, 'img_del']);
        //do_action('del_img_profile');
    }

    private static function imgUrl($img_id,$id)
    {
        $url_set = get_rest_url( 0, '/dental/v1/set-img-list');
        $url_del = get_rest_url( 0, '/dental/v1/del-img-list');
        self::$user_id = wp_get_current_user()->ID;
        $form = (self::$id_wp_user == self::$user_id)?
            '<form id="img_upload'.$id.'" method = "POST" action = "'.$url_set.'" enctype = "multipart/form-data">
                                <div class="form-group">
                                    <label  for="img_list" class="sr-only">Выбрать</label>
                                    <input type = "file" class="img_list" name="img_god_upload" multiple="false" accept="image/jpeg,image/png,image/gif"/>
                                    <input type="hidden" id="id_god" name="id_god" value="'.$id.'"  />
                                    <input type="hidden" id="id_post" name="id_post" value="'.self::$id_post.'"  />'.
                                    wp_nonce_field('img_god_upload', 'nonce_img_upload').
                                '</div>
             </form>' : NULL;
        $form_del = (self::$id_wp_user == self::$user_id)? '<form id="img_delete'.$img_id.'" method="get" action="'.$url_del.'">
                       <input type="hidden" id="id_img" name="id_img" value="'.$img_id.'"  />
                       <input type="hidden" id="id_list" name="id_list" value="'.$id.'"  /> '
                       . wp_nonce_field('img_god_del', 'nonce_img_del').' 
                       <input type="button" id="img_god_submit" onclick="imgSubmit('.$img_id.')" name="img_del_submit" value="удалить" />
                       </form>' : NULL;
        return (is_null($img_id))? $form : wp_get_attachment_image($img_id, 'list_gods_img', false, array('class' => 'list-gods-img', 'data-image' => wp_get_attachment_url($img_id))).$form_del;

    }

    public static function set_img_upload()
    {
        /*
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
        }*/
    }

    public static function img_del()
    {
        /*if(isset($_GET['img_del_submit']) && wp_verify_nonce($_GET['nonce_img_del'], 'img_god_del') && self::$id_wp_user === self::$user_id)
        {
            wp_delete_attachment( $_GET['id_img'], true);
            Profile_user::del_img_list($_GET['id_list']);
        }*/
    }

}