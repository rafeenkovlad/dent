<?php


namespace Outpost;


class HelpPost
{
    public $wpPostsId, $email, $infoQueryCompany, $metaName, $contacts;
    private static $query;
    /*
    public function __coustruct($wpPostsId, $email, $infoQueryCompany, $metaName, $contacts)
    {
        $this->wpPostsId = $wpPostsId;
        $this->email = $email;
        $this->infoQueryCompany = $infoQueryCompany;
        $this->metaName = $metaName;
        $this->contacts = $contacts;
    }*/

    public function setPost()
    {
        return self::$query = "INSERT INTO wp_posts (post_content, post_title, post_date) VALUES (:text, :meta_name, :post_date)";
    }

    public function setPostInfo()
    {
        return self::$query = "INSERT INTO posts_helps (wp_posts_id, email, info_query_company, meta, contacts) VALUES (:wp_posts_id, :email, :info_query_company, :meta, :contacts);";
    }
}