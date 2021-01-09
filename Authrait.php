<?php


namespace Author\rait;


class Authrait
{
    //obj information raiting author all posts
    public $arrRait, $id, $setRait, $who;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getArrLike()
    {
        return $this->arrRait = "SELECT like_array FROM rait WHERE wp_users_id = :id ";
    }

    public function getCount($arr)
    {
        foreach($arr as $sirialize){
            $count[] =&  json_decode($sirialize, TRUE);
        }

        for($i = 0; $i < sizeof($count); $i++){
            $sum[] = sizeof($count[$i]);
        }
        return array_sum($sum);
    }

    public function userExists($db)
    {
        $isset = $db->prepare("SELECT EXISTS(SELECT wp_users_id FROM like_sum WHERE wp_users_id = :id)");
        $isset->execute(['id' => $this->id]);
        if($isset->fetch(\PDO::FETCH_NUM)[0] == '0' )
        {
            return $this->setRait = "INSERT INTO like_sum (wp_users_id, like_col) VALUES (:id, :count)";
        }else{
            return $this->setRait = "UPDATE like_sum SET like_col = :count WHERE wp_users_id = :id";
        }
    }
}