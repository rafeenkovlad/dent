<?php
namespace Form\rait;

use App\Models\{Like_count_profile, Rait_count};

class Rait_short extends \Db
{
    private $objAuthor, $objUser, $post_id;

    public function __construct()
    {
        $this->objUser =  get_user_by('ID', get_current_user_id());

        add_action('wp_enqueue_scripts', function(){
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
        require_once('likes/index.html');
    }

    private function get_count_like()
    {
        $this->post_id = get_the_ID();
        $this->objAuthor = get_user_by('login', get_the_author_meta('login'));
        $this->func()->likeData($this->post_id, $this->objAuthor->data->ID, [$this->objUser->data->ID => $this->objUser->data->display_name]);
        print_r(': '.sizeof($this->func()->likeUser($this->db())));

        if($_POST['addlike'] === '+1')$this->add_like();

    }

    private function user_liked($usersLiked = Rait_count::class)
    {
        $usersLiked = $usersLiked::where('wp_posts_id', '=', $this->post_id)
            ->select('like_array')
            ->get()[0]->original['like_array'];
        print_r($usersLiked);
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