<?php
namespace Filter\profile;

use App\Models\{Company, Worker, Gods, Like_count_profile, Wp_post};
use Form\profile\{User_profile, User_profile_static};

class Profile_user extends \Db
{
    protected $namespace, $route, $args;
    public function __construct($login_exists) //только для зарегистрированных пользователей
    {
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        //регистрируем новый размер изображения каталога товаров
        add_image_size( 'list_gods_img', 64, 64, true );

        //для авторизованных
        add_action('rest_api_init', [&$this, 'register_route']);

        $this->namespace = 'profile/v1';
        $this->route = '/set';
        $this->args =[
            'methods' => 'POST',
            'callback' => [$this, 'update_profile'],
            'permission_callback' => fn()=> $login_exists>0
        ];

        add_action('plugins_loaded', [&$this, 'regProfileChortcode']);

        //для не авторизованных
        add_action('plugins_loaded', [&$this, 'regProfileChortcodeStatic']);
    }

    /*private function console_log($data){ // сама функция
        if(is_array($data) || is_object($data)){
            echo("<script>console.log('php_array: ".json_encode($data)."');</script>");
        } else {
            echo("<script>console.log('php_string: ".$data."');</script>");
        }
    }*/

    public function register_route()
    {
        register_rest_route($this->namespace, $this->route, $this->args);
    }

    public function update_profile(\WP_REST_Request $request)
    {
        Company::where('wp_users_id', $request['wp_user_id'])
            ->update(['name_company' => $request['name_profile'], 'contact' => $request['contact_profile'], 'info' => $request['info_profile']]);
        $this->func()->setImgCompany($this->db(), $_FILES['img'], $request['wp_user_id']);

        Worker::where('wp_users_id', $request['wp_user_id'])
            ->update(['full_name' => $request['name_profile'], 'contact' => $request['contact_profile'], 'about_your' => $request['info_profile']]);
        $this->func()->setImgWorker($this->db(), $_FILES['img'], $request['wp_user_id']);

        //устанавливаем миниатюру статической записи профиля
        $id_img = media_handle_upload( 'img', $request['post_id'] );
        set_post_thumbnail($request['post_id'], $id_img);
    }

    protected static function getUserProfileCompanyObject($id = null, $profile = Company::class)
    {
        return $profile::where('wp_users_id', $id??get_current_user_id())
            ->take(1)
            ->get();
    }

    protected static function getUserProfileWorkerObject($id = null, $profile = Worker::class)
    {
        return $profile::where('wp_users_id', $id??get_current_user_id())
            ->take(1)
            ->get();
    }

    public function regProfileChortcode()
    {
        $getUserProfileCompanyObject = (sizeof(self::getUserProfileCompanyObject()) == 0) ? null : self::getUserProfileCompanyObject();
        User_profile::profile($getUserProfileCompanyObject ?? self::getUserProfileWorkerObject());
    }

    //шорткод для получения информации о профиле для статической записи.
    public function regProfileChortcodeStatic()
    {
        User_profile_static::user_profile();
    }

    public static function getUserProfileObject($id)
    {
        $profile = (sizeof(self::getUserProfileCompanyObject($id)) == 0)? self::getUserProfileWorkerObject($id) : self::getUserProfileCompanyObject($id);
        return $profile;
    }

    public static function getUserListGods($id, $list = Gods::class)
    {

        return $list::where('company_id', $id)->get();
    }

    //Отправка csv-list
    public function setListCsv()
    {
        if(isset($_POST['sub'])) {
            $this->func()->sendCSV($this->db(), $_FILES['csv']['tmp_name'], get_current_user_id());
        }
    }

    //запись урла картинки в таблицу excel_list столбец img_url
    public static function setImgUrlList($id, $attachment_id, $list = Gods::class)
    {
        return $list::where('id', $id)
            ->update(['img_url' => $attachment_id]);
    }

    //Получить все лайки по статьям, автора
    public static function like_sum_author($user_id, $like = Like_count_profile::class)
    {
        return $like::where('wp_users_id', '=', $user_id)
            ->get()[0]->like_col;

    }

    //Получить список постов
    public static function get_user_posts($user_id, $posts = Wp_post::class)
    {
        return $posts::where('post_author', $user_id)
            ->where('post_type', 'post')
            ->get();
    }

    //удалить изображение из excel list
    public static function del_img_list($list_id, $list = Gods::class)
    {
        return $list::where('id', '=', $list_id)
            ->update(['img_url' => NULL]);
    }
}