<?php
namespace Form\rait;

use App\Models\{Like_count_profile, Rait_count};
use WP_Query;

class Rait_short extends \Db
{
    private $objAuthor, $objUser, $post_id;

    public function __construct()
    {
        $this->objUser =  get_user_by('ID', get_current_user_id());

        add_action('wp_enqueue_scripts', function(){
            wp_register_style('like_css_normalize', plugins_url('db/Form/likes/normalize.css'));
            wp_enqueue_style('like_css_normalize');
            wp_register_style('like_css', plugins_url('db/Form/likes/style.css'));
            wp_enqueue_style('like_css');
            wp_register_script('header_script_js', plugins_url('db/Form/likes/header-script.js'));
            wp_register_script('likes_min_js', plugins_url('db/Form/likes/jquery.min.js'));
            wp_enqueue_script('likes_min_js');
        });
        add_action('wp_footer', function() {
            wp_enqueue_script('header_script_js');
        });

        add_shortcode('likes_post', [&$this, 'get_likes_form']);
    }

    public function get_likes_form()
    {
        ob_start();
        require_once('likes/index.html');
        return ob_get_clean();
    }

    private function get_count_like()
    {
        $this->post_id = get_the_ID();
        $this->objAuthor = get_user_by('login', get_the_author_meta('login'));
        $this->func()->likeData($this->post_id, $this->objAuthor->data->ID, [$this->objUser->data->ID => $this->objUser->data->display_name]);
        print_r(sizeof($this->func()->likeUser($this->db())));

        if($_POST['addlike'] === '+1')
            [
                'auth' => $auth = is_user_logged_in(),
                'add' => ($auth)?$this->add_like(): print_r('вы должны быть авторизованны')
            ];


    }

    private function user_liked($usersLiked = Rait_count::class)
    {
        $usersLiked = $usersLiked::where('wp_posts_id', '=', $this->post_id)
            ->select('like_array')
            ->get()[0]->original['like_array'];
        $liked = json_decode($usersLiked , true);
        $count =sizeof($liked)-1;
        $i=0;
        foreach($liked as $key => $user){
            $object = new WP_Query('category_name=mp&author='.$key);
            $url = get_permalink($object->posts[0]->ID);
            print_r("<a href ='$url'>{$user}</a>");
            if($i != $count)print_r(", ");
            $i++;
        }

    }

    private function add_like($raitCount = Rait_count::class, $userLikeSumExist = Like_count_profile::class)
    {
        $addlike = $this->func()->setRait($this->db());

        if($addlike) {
            $query = $raitCount::TotalLikeUser($this->objAuthor->data->ID)
                ->get();
            foreach ($query as $objLike) {
                $likeArr[] = sizeof(json_decode($objLike->original['like_array'], JSON_OBJECT_AS_ARRAY));
            }

            $totalLike = 0;
            foreach ($likeArr as $postLike) {
                $totalLike += $postLike;
            }

            $updateLike = $userLikeSumExist::where('wp_users_id', '=', $this->objAuthor->data->ID)
                ->update(['like_col' => $totalLike]);

            //var_dump($updateLike);

            $totalLikeTable = new Like_count_profile();
            if ($updateLike == 0) {
                $totalLikeTable->wp_users_id = $this->objAuthor->data->ID;
                $totalLikeTable->like_col = $totalLike;
                $totalLikeTable->save();
            }
        }

    }
}