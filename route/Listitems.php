<?php
namespace Route\Wp;
use Filter\profile\Profile_user;

class Listitems extends \Form\profile\User_profile_static
{
    public function __construct()
    {
        add_action('rest_api_init', [$this,'register_route']);
    }

    public function register_route()
    {
        register_rest_route('dental/v1', '/get-list-items', $this->args());
    }

    private function args()
    {
        return [
            'methods' => 'GET',
            'callback' => [$this,'loadPage']
        ];
    }

    public function loadPage()
    {
        //$id = json_decode(str_replace('\\','', $_GET['user'] ));
        if(!isset(self::$user_id)) {
            self::$user_id = $_GET['user_id'];
            self::$id_wp_user = $_GET['id_wp_page'];
            self::$id_post = $_GET['id_post'];
            self::$nonce_img_upload = "<input type='hidden' id='nonce_img_upload' name='nonce_img_upload' value='{$_GET['nonce_img_upload']}'>
                                    <input type='hidden' name='_wp_http_referer' value='/stomshop/'>";
            self::$nonce_img_del = "<input type='hidden' id='nonce_img_del' name='nonce_img_del' value='{$_GET['nonce_img_del']}'>
                                    <input type='hidden' name='_wp_http_referer' value='/stomshop/'>";
            Profile_user::getUserListGods($_GET['id_wp_page']);
            self::$list = Profile_user::$page;
            self::$profile = Profile_user::getUserProfileObject($_GET['id_wp_page'])[0];
        }
        $page = $_GET['page']??0;
        $this->get_page($page = parent::$list[$page]);
    }

    private function get_page($page)
    {
        //self::$list = $page;
        ?>
        <?php foreach ($page as $object): ?>
        <tr>
            <td><?= $object->id ?></td>
            <td class="profile_click_chat"><?= $object->name ?></td>
            <td class="profile_click_sn"><?= $object->sirial_number ?></td>
            <td><?= $object->made_in_company ?></td>
            <td><?= $object->price ?></td>
            <td><div class="read-more js-read-more" data-rm-words="10"><?= $object->litle_info ?></div></td>
            <td><?= (self::$profile->name_company !== NULL) ? self::$profile->name_company : self::$profile->full_name ?></td>
            <td><?= self::imgUrl($object->img_url,$object->id)?></td>
        </tr>
    <?php endforeach; ?>
<?php

    }


}
