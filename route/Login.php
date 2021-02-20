<?php
namespace Route\rest;
use Coderun\ContentCabinet\AuthJwt;


class Login
{
    public function __construct()
    {
        echo getcwd();
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
        add_action('rest_api_init', [&$this, 'register_routes']);
    }

    public function register_routes()
    {

        register_rest_route('dental/v1', '/login',
            [
                'methods' => 'POST',
                'callback' => [&$this,'pre_login'],
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
            $response = rest_ensure_response( ['success' => true, 'response' => $response, 'params' => $return_params] );
            $response->set_status( 401 );
            return $response;
        }
        //Класс-обёртка над библиотекой jwt
        $jwt=new AuthJwt();

        $return_params['user_id'] = $user->data->ID;

        $return_params['token']=$jwt->create($user->data->ID);//Создание токена и в качестве полезной нагрузке - ИД пользователя

        $return_params['token_refresh']=$jwt->createRefresh($user->data->ID);//За одно создадим рефреш-токен

        $return_params['redirect']='/admin';//на будущее

        //Сохраним в заранее подготовленную таблицу MySQL наши данные
        $jwt->addTable([
                'user_id'=>intval($user->data->ID),
                'auth_token'=>$return_params['token'],
                'refresh_token'=> $return_params['token_refresh'],
            ]
        );

        if($this->debug) {//Расширенный вывод информации
            $return_params=array_merge($return_params,$request_param,[$user]);
        }
        //Подготовка объекта response WordPress
        $response = rest_ensure_response( ['success' => true, 'response' => $response, 'params' => $return_params] );

        $response->set_status( 200 );
        //Возвращаем заголовки с токеном и рефреш-токеном клиенту
        $response->set_headers( [
                $this->header_token_key         => $return_params['token'],
                $this->header_token_refresh_key => $return_params['token_refresh'],
            ]
        );

        return $response;
    }
    /*--------------------------------------------------
    Для аутентификации пользователя на стороне WordPress используем специальный метод (он же используется в роутах).
    Суть метода проверки в WordPress, в том что бы он вернул булево
    значение – этакий ключ к разрашению дальнейшего выполнения колбэка этого роута.*/
    public function checkAuth(\WP_REST_Request $request) {

        $token=$request->get_params($this->header_token_key)['token'];//токен из заголовка
        $token_refresh=$request->get_params($this->header_token_refresh_key)['token_refresh'];//рефреш-токен из заголовка

        $jwt=new AuthJwt();//Наша обёртка

        $is_valid_token=$jwt->validate($token);//Проверка валидности токена

        if($is_valid_token['isValid']??false) {
            return true;
        }

        if(empty($token_refresh)) {
            return false;
        }

        $jwt=new AuthJwt();

        if($jwt->getUserIdToRefreshToken($token_refresh)===0) {
            return false;
        }

        $this->is_refresh=true;//флаг что унжно делать обновление токена

        return true;

    }

    /**
     * Обновляет токен
     * Заполняет заголовки для клиента
     * @param \WP_REST_Request $request
     * @param \WP_REST_Response $response
     */
    protected function responseRefreshToken(\WP_REST_Request $request,\WP_REST_Response &$response):void {
        var_dump($request);
        $token_refresh=$request->get_header($this->header_token_refresh_key);

        if(empty($token_refresh)) {
            return;
        }

        $jwt=new AuthJwt();

        $user_id=$jwt->getUserIdToRefreshToken($token_refresh);//ИД пользователя по токена-рефреша

        if(empty($user_id)) {
            return;
        }

        $user=new \WP_User($user_id);

        if(empty($user)) {
            return;
        }

        $return_params['token']=$jwt->create($user->ID);

        $return_params['token_refresh']=$jwt->createRefresh($user->ID);

        $jwt->addTable([
                'user_id'=>intval($user->ID),
                'auth_token'=>$return_params['token'],
                'refresh_token'=> $return_params['token_refresh'],
            ]
        );

        $response->set_headers( [
                $this->header_token_key      => $this->token['token'],
                $this->header_token_refresh_key      => $this->token['token_refresh'],
            ]
        );

    }

    public function token_actual()
    {
        add_action('rest_api_init', [&$this, 'route_token_available']);
    }
    public function route_token_available()
    {
        register_rest_route('token/v1', '/activ', [
            'methods' => 'POST',
            'callback' => [&$this, 'adminTest'],
            'permission_callback' => [&$this, 'checkAuth']//проверка токенов
        ]);


    }
    //Метод класса (обратите внимание!)
    public function adminTest(\WP_REST_Request $request): object
    {
        $response = rest_ensure_response(['success' => true, 'response' => 'ok', 'params' => [$request->get_params($this->header_token_key)]]);
        if ($this->is_refresh) {// пересоздание токена - если истёк и есть валидный рефреш
            $this->responseRefreshToken($request, $response);
        }

        $response->set_status(200);

        return $response;
    }

    public function set_time_cookie_wp($user_id)
    {
        apply_filters('auth_cookie_expiration', $user_id, false );
        add_filter( 'auth_cookie_expiration',  [&$this,'cookie_expiration_new'], 20, 3 );

    }
    public function cookie_expiration_new ($expiration, $user_id, $remember ) {
        // Время жизни cookies для администратора
        $expiration =  60; //* DAY_IN_SECONDS;
        if (user_can( $user_id, 'manage_options' ) ) {
            return $expiration; //- 84500;
        }
        // Для всех остальных пользователей

        return $expiration;
    }
}





