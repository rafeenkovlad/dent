<?php
namespace Filter\login;

class Login_create extends \Db{

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
            Write_profile::set_profile_reg();
            if(isset($_POST['set_save_info'])):

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
            $this->login()->login_dent();

            $login = new WP_REST_Request('POST', '/dental/v1/login');
            $login->set_query_params([
                'login' => $request['login'],
                'password' => $request['password']
            ]);
            $response = rest_do_request($login);

            if(isset($response->data['response'])){
                setcookie('auth[token]', json_encode($response, true), time() + 60);
            }
        }

        if($_COOKIE['auth']['token']){
            $this->login()->token_actual();
            $this->token_proov();
        }

        if(!isset($_COOKIE['auth']['token']) && !empty($request['login']) && isset($request['sub'])){
            var_dump('Здесь будет сброс пароля');
        }
    }

    public function token_proov()
    {
        $cookie = json_decode($_COOKIE['auth']['token'], true);

        if($cookie):

            $login = new WP_REST_Request('POST', '/token/v1/activ');
            $login->set_query_params([
                'token' => $cookie['data']['params']['token'],
                'token_refresh' => $cookie['data']['params']['token_refresh'],
                'user_id' => $cookie['data']['params']['user_id']
            ]);
            $response = rest_do_request($login);
            $this->login()->set_time_cookie_wp($response->data['params'][0]['user_id']); //изменяем время жизни куки токена, заданного вордпресс
            wp_set_auth_cookie($response->data['params'][0]['user_id']); //авторизуем пользователя


            var_dump($_COOKIE);
        endif;
    }
}

