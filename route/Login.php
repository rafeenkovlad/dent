<?php
namespace Route\rest;

class Login
{
    public function __construct()
    {

        //echo getcwd();
        if(preg_match('/wp-admin/i', getcwd()))
        {
            require_once('../wp-includes/pluggable.php');
            require_once('includes/class-wp-site-health.php');
        }else{
            require_once('wp-includes/pluggable.php');
            require_once('wp-admin/includes/class-wp-site-health.php');
        }
    }

    public function login_dent()
    {
        add_action('rest_api_init', [&$this, 'register_route']);
    }

    public function register_route()
    {
        register_rest_route('/dental/v1', '/login',
            [
                'methods' => 'POST',
                'callback' => [&$this,'pre_login'],
                'permission_callback' => '__return_true',
                'args' =>
                    [
                        'login' =>
                            [
                                'default'           => null,
                                'required'          => true,
                                'validate_callback' => function ($param)
                                {
                                    if(empty($param))
                                    {
                                        return false;
                                    }
                                    return true;
                                },
                                'sanitize_callback' => function ($param)
                                {
                                    return trim($param);
                                }
                            ],
                        'password'=>[
                            'default'           => null,
                            'required'          => true,
                            'validate_callback' => function($param){
                                if(empty($param) || strlen($param)<5) {

                                    return false;
                                }
                                return true;
                            },
                            'sanitize_callback' => function($param){
                                return trim($param);
                            }
                        ]
                    ]
            ]);
    }
    public function pre_login(\WP_REST_Request $request):object
    {

        $return_params=[];

        $response= 'ok';

        $request_param=$request->get_params();


        $user=wp_authenticate($request_param['login']??null,$request_param['password']??null);

        if(is_wp_error($user)){
            $response='Не верный логин или пароль';
            $user=null;
            $response = rest_ensure_response( ['success' => true, 'response' => $response, 'user'=> $user, 'params' => $return_params] );
            $response->set_status( 401 );
            return $response;
        }


        $return_params['user_id'] = $user->data->ID;

        $return_params['redirect'] = get_home_url();//на будущее

        //Подготовка объекта response WordPress
        $response = rest_ensure_response( ['success' => true, 'response' => $response, 'user' => true, 'params' => $return_params] );

        $response->set_status( 200 );

        return $response;
    }


    public function set_time_cookie_wp($user_id)
    {
        apply_filters('auth_cookie_expiration', $user_id, false );
        add_filter( 'auth_cookie_expiration',  [&$this,'cookie_expiration_new'], 20, 3 );

    }
    public function cookie_expiration_new ($expiration, $user_id, $remember ) {
        // Время жизни cookies для администратора
        $expiration =  86400; //* DAY_IN_SECONDS;
        if (user_can( $user_id, 'manage_options' ) ) {
            return $expiration; //- 84500;
        }
        // Для всех остальных пользователей
        return $expiration;
    }


}





