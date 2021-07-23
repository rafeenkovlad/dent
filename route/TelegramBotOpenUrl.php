<?php
namespace Route\Wp;
use Form\alert\Alert;

class TelegramBotOpenUrl extends \Db
{
    public function __construct()
    {
        add_action('rest_api_init', [&$this, 'register_route']);
    }

    public function register_route()
    {
        register_rest_route('dental/v1', '/get-telegram-url', $this->args());

    }

    private function args()
    {
        return [
            'methods' => 'GET',
            'callback' => [&$this, 'searchProfile']
        ];
    }

    public function searchProfile()
    {
        if(isset($_GET['id_user']) and isset($_GET['sn']))
        {
            $query = new \WP_Query('cat=2');
            foreach($query->posts as $pageProfile)
            {
                if ($pageProfile->post_author === $_GET['id_user']): return $this->redirect($pageProfile);
                else:
                    wp_redirect(get_site_url(null, '', 'https'));
                    Alert::doactionAlert('Такого пользователя больше не существует.', 'Не удалось найти:');
                    exit;
                endif;
            }
        }
    }

    private function redirect($pageProfile)
    {
        wp_redirect(get_page_link($pageProfile->ID).'?sn='.$_GET['sn']);
        exit;
    }
}