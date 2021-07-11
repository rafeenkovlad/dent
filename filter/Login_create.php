<?php
namespace Filter\login;
use function Password\generated\passGen\passGenerated;
use Form\alert\Alert;
use Route\rest\Login;
use Dbdental\reg\Reg;
use \Wp_user as User;

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
        if (isset($_GET['email']) && $_GET['reg'] === 'success') Alert::doactionAlert('Данные сохранены и отправлены на вашу почту: '.$_GET['email'], 'Регистрация прошла успешно:');
        if(!empty($request['repass']) && isset($request['sub']) && !empty($request['password'])) {

            $regWarExists = new Reg($request['login'], $request['password'], $request['repass']);
            $warning = $regWarExists->warningValid($regWarExists->validInput());

            if(is_string($warning))
            {
                Alert::doactionAlert($warning, 'Ошибка:');
            }else{
                parent::myprofileWrite();
            }

            // Создаем массив данных новой записи
            $post_data = array(
                'post_title'    => sanitize_text_field( $_POST['name_dental'] ),
                'comment_status' => 'closed',
                'post_status'   => 'publish',
                'post_content' => '[profile_list][/profile_list]'
            );

                if ($request['user'] == 'company') {
                    $this->func()->dataReg($request['login'], $request['password'], $request['repass']);
                    $id_wp_user = $this->func()->regCompany($this->db());
                    $this->func()->dataCompany([$_POST['name_dental'], 'test', $_POST['contact'], $_POST['message'], $this->db()->lastInsertId()]);
                    $this->func()->companySet($this->db());
                    // Вставляем запись в базу данных
                    $post_data['post_author'] = $id_wp_user;
                    $post_id = wp_insert_post( $post_data );
                    //запишем ид записи для назначения миниатюры
                    $this->func()->setIdPostCompany($this->db(), $post_id, $id_wp_user);
                    wp_set_object_terms( $post_id, 'Profile', 'category');
                    wp_set_post_tags($post_id, 'company');
                    //Добавить роль
                    $user = new User( $id_wp_user );
                    $user->add_role( 'contributor' );

                    $this->func()->dataEmail($_POST['email']);
                    $this->func()->companyEmail($this->db(), $id_wp_user, $_POST['name_dental']);
                }
                if ($request['user'] == 'worker') {
                    $this->func()->dataReg($request['login'], $request['password'], $request['repass']);
                    $id_wp_user = $this->func()->regWorker($this->db());
                    $this->func()->dataWorker([$_POST['name_dental'], 'test', $_POST['contact'], $_POST['message'], $this->db()->lastInsertId()]);
                    $this->func()->workerSet($this->db());
                    // Вставляем запись в базу данных
                    $post_data['post_author'] = $id_wp_user;
                    $post_id = wp_insert_post( $post_data );
                    //запишем ид записи для назначения миниатюры
                    $this->func()->setIdPostWorker($this->db(), $post_id, $id_wp_user);
                    wp_set_object_terms( $post_id, 'Profile', 'category');
                    wp_set_post_tags($post_id, 'worker');
                    //Добавить роль
                    $user = new User( $id_wp_user );
                    $user->add_role( 'contributor' );

                    $this->func()->dataEmail($_POST['email']);
                    $this->func()->companyEmail($this->db(), $id_wp_user, $_POST['name_dental']);
                }


        }

        //Авторизация пользователя
        if(!empty($request['password']) && empty($request['repass']) && isset($request['sub'])){
            $this->login->login_dent();

            $login = new \WP_REST_Request();
            $login->set_method('POST');
            $login->set_route('/dental/v1/login');
            $login->set_query_params([
                'login' => $request['login'],
                'password' => $request['password']
            ]);

            $response = rest_do_request($login);

            [//проверяем пару логин пароль
                'login_in_success' => $response->data['user']??Alert::doactionAlert($response->data['response'], 'Ошибка:'),

            //проверяем на содержимость необходимых ключей
                'params_exists' => $validate = $this->array_key_exists_auth($response->data['params']),
            //авторизуем польхователя
                'auth_user' => $auth = ($validate)? wp_set_auth_cookie($response->data['params']['user_id']): false,
            //время жизни кук
                'timeout' => $this->login->set_time_cookie_wp($response->data['params']['user_id']),
            //редирект
                'redirect' => $auth??wp_safe_redirect($response->data['params']['redirect']),
                //'redirect_end' => exit
            ];


        }

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
