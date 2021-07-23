<?php
namespace Route\Wp;
use Filter\profile\Profile_user;

class DelImgList
{
    private static $nonce;
    static function register_route()
    {
        self::$nonce = wp_verify_nonce($_GET['nonce_img_del'], 'img_god_del');
        add_action('rest_api_init', function(){
            register_rest_route('/dental/v1', '/del-img-list', self::get_args());
        });
    }

    static function get_args()
    {

        return [
            'methods' => 'GET',
            'callback' => [DelImgList::class, 'del_img'],
            'permission_callback' => '__return_true'
        ];
    }

    static function del_img()
    {
        file_put_contents(__DIR__ . '/message.txt', print_r($_GET, true));

        if(isset($_GET['nonce_img_del']) && self::$nonce )
        {
            wp_delete_attachment( $_GET['id_img'], true);
            if(Profile_user::del_img_list($_GET['id_list'])):
                $success = "<p style='font-size:13px; color:green;'>Удалено</p>";
            else:
                $error = "<p style='font-size:13px; color:red;'>Ошибка</p>";
            endif;

            $data = [
                'error' => $error,
                'success' => $success
            ];


            header('Content-Type: application/json');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            exit();

        }

    }

}