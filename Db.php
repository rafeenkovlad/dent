<?php
/*
 * Plugin Name: dentaline
 * Author: Vladislav Rafeenko <rafeenkovlad@gmail.com>
 * Description: dentaline plugin
 * URI: http://localhost
 * Version: 0.1
 */
require_once 'vendor/autoload.php';
use Dbdental\db\Connect;
use FunctionCommand\Functions;
use Form\regform\Regform;
use Myprofile\write\Write_profile;
use Filter\login\Login_create;
use Filter\profile\Profile_user;
use App\Models\{Wp_user, Company};
use function App\config\loaders\bootstrap;
use \Filter\newpost\New_post;
use Form\rait\Rait_short;
use Route\Wp\TelegramBotOpenUrl;
use Form\chat\Telegramgroup;
use Form\{Search_parts, Custom_menu};
use Metabox\RmMetaBox;
use Category\Category;

class Db extends WP_REST_Controller {

    protected $db;
    public $func;
    public $getreg;
    public $token, $token_refresh, $login;
    protected $ORM;
    protected $profile;
    protected $newpost;
    public $likes;
    public $telegramUrl;
    protected static $logined, $errors;

    public function __construct(){

        register_activation_hook( __FILE__, [&$this,'dental_plugin_activate']);

        //add_filter( 'posts_where', [&$this, 'true_hide_attachments_from_another_author'] );

        self::$errors = new WP_Error();

        //Eloquent connect dental DB
        if(empty($this->ORM))
        {
            $this->ORM = bootstrap();
        }
        return $this->ORM;



    }

    protected static function errors($code, $message)
    {
        self::$errors->add($code, $message);
    }


    private function dental_plugin_activate()
    {
        flush_rewrite_rules();
    }

    protected function db()
    {
        if(empty($this->db))
        {
            $this->db = Connect::getConnect();
        }
        return $this->db;
    }

    public function func()
    {
        if(empty($this->func))
        {
            $this->func = new Functions();
        }
        return $this->func;
    }

    protected static function getreg()
    {
        self::$logined = is_user_logged_in();
        return (!self::$logined)? Regform::get_reg_form() : self::errors('logined_dental', '?????? ??????????????????????');
    }

    protected static function myprofileWrite() // login_create.php
    {
        Write_profile::set_profile_reg();
    }

    public function login_dentaline()
    {
        $this->login = new Login_create();
        $this->login->action_reg();
        (!$this->logined)? self::getreg(): self::errors('profile_dent', '???? ???????????????????? ???????????? ??????????????');
    }

    protected static function resetUserPassGet($login, $user = Wp_user::class)
    {
        return $user::where('user_login', $login)
            ->take(1)
            ->get(['ID', 'user_email']);
    }

    public function dent_profile()
    {
        $this->profile = (empty($this->profile))? new Profile_user(is_user_logged_in()): $this->profile;
        return $this->profile;
    }

    public function new_post()
    {

        if(empty($this->newpost))
        {
            return $this->newpost = new New_post();
        }
        return $this->newpost;
    }

    public function likes_post()
    {
        return $this->likes = new Rait_short();
    }

    public function telegramBotRoute()
    {
        if(empty($this->telegramUrl))
        {
            return $this->telegramUrl = new TelegramBotOpenUrl();
        }
        return $this->telegramUrl;
    }

    public function getChatTelegram($chat = Telegramgroup::class)
    {
        $chat::getTemplate();
    }

    public function search_sn_form()
    {
        new Search_parts();
    }

    public function disable_metabox($metabox = RmMetaBox::class)
    {
        $metabox::hueman_post_options_remoove();
    }

    public function category_right ($category = Category::class)
    {
        $category::clouse_category_on_new_post();
    }

    public function get_menu($menu = Custom_menu::class)
    {
        $menu::connect_menu();
    }

}

$db = new Db();
$db->login_dentaline();//??????????????????????, ??????????????????????, ???????????????? ??????, ???????????????????? ???????????? ?? ?????????? ???????????????????????????????????? ??????????????????????????
$db->dent_profile();
$db->new_post();
$db->likes_post();
$db->telegramBotRoute();
$db->getChatTelegram();
$db->search_sn_form();
$db->disable_metabox();
$db->category_right();
$db->get_menu();



?>

