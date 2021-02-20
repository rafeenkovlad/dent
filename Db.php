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


class Db extends WP_REST_Controller {

    private $db;
    public $func;
    public $getreg;
    private $controller;
    public $token, $token_refresh;


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
        $this->getreg()->get_reg_form();

    }

    public function action_reg()
    {
        $request = [
            'login' => $_REQUEST['login'],
            'password' => $_REQUEST['password'],
            'repass' => $_REQUEST['repass'],
            'user' => $_REQUEST['user'],
            'sub' => $_REQUEST['reg_dental_sub']
        ];
        add_action('init_form_dental', [&$this,'setReg']);
        do_action('init_form_dental', $request);
    }

    //  отправляем данные о регистрации
    public function setReg(array $request)
    {
        if(!empty($request['repass']) && isset($request['sub'])) {
            if ($request['user'] == 'company') {
                $this->func()->dataReg($request['login'], $request['password'], $request['repass']);
                $this->func()->regCompany($this->db());
            }
            if ($_REQUEST['user'] == 'worker') {
                $this->func()->dataReg($request['login'], $request['password'], $request['repass']);
                $this->func()->regWorker($this->db());
            }

            var_dump($this->db()->lastInsertId());
        }else{
            $this->login()->login_dent();
            $this->login()->token_actual();

            $login = new WP_REST_Request('POST', '/dental/v1/login');
            $login->set_query_params([
                'login' => $request['login'],
                'password' => $request['password']
            ]);
            $response = rest_do_request($login);
            if(isset($response->data['response'])){
                setcookie('auth[token]', json_encode($response, true), time() + 60);
            }
            $this->token_proov();
        }
    }

    public function token_proov()
    {
        $cookie = json_decode($_COOKIE['auth']['token'], true);
        if($cookie):

        $login = new WP_REST_Request('POST', '/token/v1/activ');
        $login->set_query_params([
            'token' => $cookie['data']['params']['token'],
            'token_refresh' => $cookie['data']['params']['token_refresh']
        ]);
        $response = rest_do_request($login);

       print_r($response->data['params'][0]);
       print_r($cookie['data']['params']);
        endif;
    }


}

$db = new Db();
$db->getRegForm();
$db->action_reg();





?>

