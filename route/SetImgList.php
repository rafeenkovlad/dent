<?php
namespace Route\Wp;
use Form\profile\User_profile_static;
use Filter\profile\Profile_user;

class SetImgList
{
    private static $nonce;
    static function register_route()
    {
        self::$nonce = wp_verify_nonce($_POST['nonce_img_upload'], 'img_god_upload');
        add_action('rest_api_init', function(){
            register_rest_route('/dental/v1', '/set-img-list', self::get_args());
        });
    }

    static function get_args()
    {

            return [
                'methods' => 'POST',
                'callback' => [SetImgList::class, 'set_img'],
                'permission_callback' => '__return_true'
            ];
    }

    static function set_img()
    {
            //file_put_contents(__DIR__ . '/message.txt', print_r($_POST, true));

            if(isset($_POST['nonce_img_upload']) && self::$nonce )
            {
                switch($_FILES['img_god_upload']['type']):
                    case 'image/jpeg':
                        $format = true;
                        break;
                    case 'image/gif':
                        $format = true;
                        break;
                    case 'image/png':
                        $format = true;
                        break;
                    default: $format = false;
                endswitch;
                if($format):
                    require_once (ABSPATH . 'wp-admin/includes/image.php');
                    require_once (ABSPATH . 'wp-admin/includes/file.php');
                    require_once (ABSPATH . 'wp-admin/includes/media.php');
                    $attachment_id = media_handle_upload('img_god_upload', $_POST['id_post']);
                    //$url = wp_get_attachment_url($attachment_id);
                    if(!is_wp_error($attachment_id)){
                        Profile_user::setImgUrlList($_POST['id_god'], $attachment_id);
                        $success = "<p style='font-size:13px; color:green;'>Загружено...</p>";
                    }else{$error = "<p style='font-size:13px; color:orangered;'>Ошибка</p>";}
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