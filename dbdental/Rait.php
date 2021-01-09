<?php


namespace Dbdental\like;


class Rait
{

    public $login;
    public $query;

    //Проверка авторизован ли пользователь
    public static function raitLogin()
    {

        /*public function __construct($inLogin)
        {
            $this->login = $inLogin;
        }*/

    }

    //Кому понравился пост
    public function getLike()
    {
        return $this->query = "SELECT like_array FROM rait WHERE wp_posts_id = :wp_post_id ";
    }

    //Запись данных об авторе и о пользователе проголосовавшем за понравившийся пост
    public function raitSetLike()
    {
        return $this->query = "INSERT INTO rait (name_co_and_wo, wp_users_id, like_array, wp_posts_id) VALUES (:name_co_and_wo, :wp_users_id, :like_array, :wp_posts_id)";
    }

    //Запись данных о пользователе проголосовавшем за понравившийся пост
    public function raitSetLikeUser()
    {
        return $this->query = "UPDATE rait SET like_array = :like_array WHERE wp_posts_id = :wp_posts_id";
    }

    //получаем имя компании или ее сотрудника
    public function getNameCompany()
    {
        return $this->query = "SELECT name_company FROM company WHERE wp_users_id = (SELECT ID FROM wp_users WHERE id =:id);";
    }

    public function getNameWorker()
    {
        return $this->query = "SELECT full_name FROM workers WHERE wp_users_id = (SELECT ID FROM wp_users WHERE id =:id)";
    }

//проверяем первый лайк или нет
    public function oneLike()
    {
        return $this->query = "SELECT wp_posts_id, like_array FROM rait WHERE wp_posts_id = :id";
    }

}