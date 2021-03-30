<?php
/*
 * Plugin Name: dentaline
 * Author: Vladislav Rafeenko <rafeenkovlad@gmail.com>
 * Description: dentaline plugin
 * URI: http://dentaline.ru
 * Version: 0.1
 */
require_once 'vendor/autoload.php';
use Dbdental\db\Connect;
use FunctionCommand\Functions;
use Form\regform\Regform;
use Myprofile\write\Write_profile;
use Filter\login\Login_create;
use App\Models\Wp_user;
use function App\config\loaders\bootstrap;



class Db extends WP_REST_Controller {

    private $db;
    public $func;
    public $getreg;
    public $token, $token_refresh, $login;
    protected $ORM;

    //Eloquent connect dental DB
    public function __construct(){
        if(empty($this->ORM))
        {
            $this->ORM = bootstrap();
        }
        return $this->ORM;
    }

    protected function db()
    {
        if(empty($db))
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
       return Regform::get_reg_form();
    }

    protected static function myprofileWrite() // login_create.php
    {
        var_dump(Write_profile::set_profile_reg());
    }

    public function login_dentaline()
    {
        $this->login = new Login_create();
        $this->login->action_reg();
        (!is_user_logged_in())? self::getreg(): 'Личный кабинет';
    }

    protected static function resetUserPassGet($login, $user = Wp_user::class)
    {
        return $user::where('user_login', $login)
            ->take(1)
            ->get(['ID', 'user_email']);
    }


}

$db = new Db();
$db->login_dentaline();//авторизация, регистрация, хранение кук, сохранение данных о новыъ зарегестрированных пользователях


?>

