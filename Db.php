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
use Route\rest\Login;
use Myprofile\write\Write_profile;
use Filter\login\Login_create;


class Db extends WP_REST_Controller {

    private $db;
    public $func;
    public $getreg;
    private $controller;
    public $token, $token_refresh, $login;


    public function db()
    {
        return $this->db = Connect::getConnect();
    }

    public function func()
    {
        if(empty($this->func))
        {
            $this->func = new Functions();
        }
        return $this->func;
    }

    public function login()
    {
        if (empty($this->controller))
        {
            $this->controller = new Login();
        }
        return $this->controller;
    }

    public function getreg()
    {
        if (empty ($this->getreg))
        {
            return $this->getreg = new Regform();
        }
        return $this->getreg;
    }
    public function getRegForm()
    {
        if(!$_COOKIE['auth']['token']){
            $this->getreg()->get_reg_form();
        }
    }

    protected static function myprofileWrite()
    {
        Write_profile::set_profile_reg();
    }

    public function login_dentaline()
    {
        $this->login = new Login_create();
        $this->login->action_reg();
    }

    private function resetUserPass()
    {

    }

}

$db = new Db();
$db->getRegForm();
$db->login_dentaline();//авторизация, регистрация, хранение кук, сохранение данных о новыъ зарегестрированных пользователях


?>

