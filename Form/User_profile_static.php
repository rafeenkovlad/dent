<?php
namespace Form\profile;

use Filter\profile\Profile_user;
use Route\Wp\{SetImgList, DelImgList, Listitems};

class User_profile_static
{
    protected static $profile;
    protected static $list, $listItems, $page;
    public static $id_wp_user, $id_post, $like_sum, $made_in_company, $price, $litle_info, $img_url, $company_id, $user_id, $count_page;
    protected static $nonce_img_upload, $nonce_img_del;

    public static function user_profile()
    {
        add_action('wp_enqueue_scripts', function(){
                //Открытие картинки в полный размер на странице с товарным листом
                wp_register_style('gods_list_css', plugins_url('db/Form/userprofile/user_profile_static/src/assets_img/css/styles.css'));
                wp_enqueue_style('gods_list_css');
                wp_register_style('bootstrap_css', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
                wp_enqueue_style('bootstrap_css');
                wp_register_script('gods_list_js', plugins_url('db/Form/userprofile/user_profile_static/src/assets_img/intense.js'));
                wp_enqueue_script('gods_list_js');
                //в разработке
                wp_register_style('userprofile_css', plugins_url('db/Form/userprofile/user_profile_static/src/style.css'));
                wp_enqueue_style('userprofile_css');
                wp_register_script('userprofile_input_js', plugins_url('db/Form/userprofile/user_profile_static/src/script.js'));

                wp_register_script('min_js', plugins_url('db/Form/js/jquery.min.js'));
                wp_register_script('readmore_js', plugins_url('db/Form/userprofile/user_profile_static/src/readmore.js'));
                wp_register_script('paginate_js', plugins_url('db/Form/userprofile/user_profile_static/src/pagination.js'));
                add_action('wp_footer', function(){

                    wp_enqueue_script('min_js');
                    wp_enqueue_script('userprofile_input_js');
                    wp_enqueue_script('readmore_js');
                    wp_enqueue_script('paginate_js');
                    wp_localize_script('paginate_js', 'selfUserId', self::$user_id);
                    wp_localize_script('paginate_js', 'selfIdPost', self::$id_post);
                    wp_localize_script('paginate_js', 'apiUrl', get_rest_url(0,'/dental/v1/get-list-items'));
                    wp_localize_script('paginate_js', 'scriptUrl', plugins_url('db/Form/'));

                });


        });

        add_shortcode('profile_list',[User_profile_static::class, 'get_list']);
        add_action('register_route_set_img', [SetImgList::class, 'register_route']);
        do_action('register_route_set_img');
        add_action('register_route_del_img', [DelImgList::class, 'register_route']);
        do_action('register_route_del_img');
        add_action('register_route_list_items', function(){
            if(empty(self::$listItems)){
                self::$listItems = new Listitems();
            }

        });
        do_action('register_route_list_items');

    }

    public static function get_list()
    {
        self::$id_post = get_the_ID();
        self::$id_wp_user = get_the_author_meta('ID');
        self::$profile = Profile_user::getUserProfileObject(self::$id_wp_user)[0];
        self::$profile->company_logo = self::$profile->company_logo ?? self::$profile->img;
        Profile_user::getUserListGods(self::$id_wp_user);
        self::$list = Profile_user::$page;
        self::$count_page = sizeof(self::$list);
        self::$user_id = wp_get_current_user()->ID;
        self::$like_sum = Profile_user::like_sum_author(self::$id_wp_user);
        self::$page = self::$list[$_GET['page']??0];
        self::$nonce_img_upload = wp_nonce_field('img_god_upload', 'nonce_img_upload');
        self::$nonce_img_del = wp_nonce_field('img_god_del', 'nonce_img_del');
        require_once ('userprofile/user_profile_static/src/index.html');
    }

    protected static function imgUrl($img_id,$id)
    {

        $url_set = get_rest_url( 0, '/dental/v1/set-img-list');
        $url_del = get_rest_url( 0, '/dental/v1/del-img-list');
        //if(!$user_id==null)self::$user_id=$user_id;
        //if(!$id_wp_user==null)self::$id_wp_user=$id_wp_user;

        $form = (self::$id_wp_user === self::$user_id)?
            '<form id="img_upload'.$id.'" method = "POST" action = "'.$url_set.'" enctype = "multipart/form-data">
                                <div class="form-group">
                                    <label  for="img_list" class="sr-only">Выбрать</label>
                                    <input type = "file" class="img_list" name="img_god_upload" multiple="false" accept="image/jpeg,image/png,image/gif"/>
                                    <input type="hidden" id="id_god" name="id_god" value="'.$id.'"  />
                                    <input type="hidden" id="id_post" name="id_post" value="'.self::$id_post.'"  />'.
                                    self::$nonce_img_upload .
                                '</div>
             </form>' : NULL;
        $form_del = (self::$id_wp_user === self::$user_id)? '<form id="img_delete'.$img_id.'" method="get" action="'.$url_del.'">
                       <input type="hidden" id="id_img" name="id_img" value="'.$img_id.'"  />
                       <input type="hidden" id="id_list" name="id_list" value="'.$id.'"  /> '
                       .self::$nonce_img_del.' 
                       <input type="button" id="img_god_submit" onclick="imgSubmit('.$img_id.')" name="img_del_submit" value="удалить" />
                       </form>' : NULL;
        return (is_null($img_id))? $form : wp_get_attachment_image($img_id, 'list_gods_img', false, array('class' => 'list-gods-img', 'data-image' => wp_get_attachment_url($img_id))).$form_del;

    }

    protected static function response_list()
    {
        $list = new \WP_REST_Request();
        $list->set_method('GET');
        $list->set_route('/dental/v1/get-list-items');

        return rest_do_request($list);
    }



}