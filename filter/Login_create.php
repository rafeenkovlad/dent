<?php
namespace Filter\login;
use function Password\generated\passGen\passGenerated;
use Form\alert\Alert;
use Route\rest\Login;

class Login_create extends \Db{

    public $login;

    public function __construct()
    {
        if (empty($this->login))
        {
            $this->login= new Login();
        }
        return $this->login;
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
        if(!is_user_logged_in()):
            add_action('init_form_dental', [&$this,'setReg']);
            do_action('init_form_dental', $request);
        endif;
    }

    //  отправляем данные о регистрации
    public function setReg(array $request)
    {
        if(!empty($request['repass']) && isset($request['sub'])) {
            parent::myprofileWrite();
            if(isset($_POST['set_save_info'])):
                var_dump('ok');

                var_dump($request);
                if ($request['user'] == 'company') {
                    $this->func()->dataReg($request['login'], $request['password'], $request['repass']);
                    $id_wp_user = $this->func()->regCompany($this->db());
                    $this->func()->dataCompany([$_POST['name'], 'test', $_POST['contact'], $_POST['message'], $this->db()->lastInsertId()]);
                    $this->func()->companySet($this->db());
                    $this->func()->dataEmail($_POST['email']);
                    $this->func()->companyEmail($this->db(), $id_wp_user);
                }
                if ($_REQUEST['user'] == 'worker') {
                    $this->func()->dataReg($request['login'], $request['password'], $request['repass']);
                    $id_wp_user = $this->func()->regWorker($this->db());
                    $this->func()->dataWorker([$_POST['name'], 'test', $_POST['contact'], $_POST['message'], $this->db()->lastInsertId()]);
                    $this->func()->workerSet($this->db());
                    $this->func()->dataEmail($_POST['email']);
                    $this->func()->companyEmail($this->db(), $id_wp_user);
                }

            endif;

        }

        //Авторизация пользователя
        if(!empty($request['password']) && empty($request['repass']) && isset($request['sub'])){
            $this->login->login_dent();

            $login = new \WP_REST_Request('POST', '/dental/v1/login');
            $login->set_query_params([
                'login' => $request['login'],
                'password' => $request['password']
            ]);
            $response = rest_do_request($login);
            [//проверяем на содержимость необходимых ключей
                'params_exists' => $validate = $this->array_key_exists_auth($response->data['params']),
            //авторизуем польхователя
                'auth_user' => $auth = ($validate)? wp_set_auth_cookie($response->data['params']['user_id']): false,
            //время жизни кук
                'timeout' => $this->login->set_time_cookie_wp($response->data['params']['user_id']),
            //редирект
                'redirect' => ($statusCookie)? wp_safe_redirect($response->data['params']['redirect']): false,
                //'redirect_end' => exit,
            ];


        }

        var_dump($_COOKIE);
        if(!empty($request['login']) && isset($request['sub']) && empty($request['password'])){
         $save = [//save new pass
               'object' => $userObj = parent::resetUserPassGet($request['login'])[0],

               'login' => $login = function($userObj)
               {
                   $userObj->user_pass = md5(passGenerated());

                   $userObj->save();
                   Alert::doactionAlert("Новый пароль отправлен на вашу почту {$userObj->user_email}.", 'Успешно:');
                   echo ('здесь должна быть отправка на email');
               },

               'isEmpty' => (empty($userObj->ID))? Alert::doactionAlert('Такого пользователя не существует!', 'Ошибка:') : $login($userObj),

           ];
         //var_dump($save);
        }
    }

    public function array_key_exists_auth($response)
    {
        $status = array_key_exists('user_id', $response);
        $status = ($status)? array_key_exists('redirect', $response): false;

        return $status;
    }
}
