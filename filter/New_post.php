<?php
namespace Filter\newpost;

use Form\profile\Write_new_post;
use Form\alert\Alert;

class New_post extends \Db
{
    public function __construct()
    {
        add_action('rest_api_init', [&$this, 'route_posts']);
        add_action('plugins_loaded', [&$this, 'prepare_posts']);
        if(isset($_POST['submit_post'])){
            $new_post = new \WP_REST_Request();
            $new_post->set_method('POST');
            $new_post->set_route('/newpost/v1/author');
            rest_do_request($new_post);
        }
    }

    public function route_posts()
    {
        register_rest_route('newpost/v1', '/author', [
            'methods' => 'POST',
            'callback' => [&$this, 'set_post'],
            'permission_callback' => fn(\WP_REST_Request $request) => is_user_logged_in()
        ]);
    }

    public function set_post()
    {
        $rubrics = ['study_or_works' => 'Учеба/работа', 'news' => 'Новости', 'useful' => 'Полезное', 'buy_or_sell' => 'Куплю/продам', 'services' => 'Сервис'];
        $key= array_filter(array_keys($rubrics),
            function($rubric)
            {
                if($rubric == $_POST['rubrica']) return $rubric;
            });

        $id = get_current_user_id();
        // Создаем массив данных новой записи
        $post_data = array(
            'post_title'    => sanitize_text_field( $_POST['title_new_post'] ),
            'post_content'  => $_POST['textarea_new_post'].'</br>[likes_post][/likes_post]',
            'post_status'   => 'publish',
            'post_author'   => &$id
        );
        if($key??false):
            //вставляем запись
            $post_id = wp_insert_post($post_data, true);
            if(is_wp_error($post_id)){
                echo $post_id->get_error_message();
            }
            //вырезаем из текстареа урл изображения
            preg_match('/(?<=src=") \S+\w+ (?=")/xsu', $post_data['post_content'], $img_url);
            $img_url = preg_replace('~-[0-9]+x[0-9]+(?=\..{2,6})~', '', $img_url[0] );
            $img_id = attachment_url_to_postid($img_url);

            //если картирки были помещены в виде галереии
            if($img_id==0) {
                preg_match('/(?<=ids=") \d+ (?=[",])/xsu', $post_data['post_content'], $img_id);
                $img_id = $img_id[0];
            }
            //назначаем миниатюру поста по первой картинке
            set_post_thumbnail($post_id, $img_id);

            wp_set_object_terms( $post_id, $rubrics[array_values($key)[0]], 'category');

            //переходим на созданный пост
            wp_redirect(get_page_link($post_id));
            exit;


        else:
            Alert::doactionAlert('Для опубликования поста необходимо выбрать рубрику.', 'Предупреждение:');
        endif;
    }

    public function prepare_posts()
    {
        Write_new_post::new_post_profile();
    }


}