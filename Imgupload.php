<?php


namespace Dbdental\img;


class Imgupload
{
    private $img;
    public $wpUserId;
    private $query;
    public function __construct($img, $wpUserId)
    {
        $this->img = $img;
        $this->wpUserId = $wpUserId;
    }

    //проверка есть ли изображение копании/работника
    public function dbImg()
    {

    }

    //подготовка загрузки изображения for Company
    public function trainImg()
    {
        $imgFuncArr = ['image/jpeg' =>imageCreateFromJpeg($this->img['tmp_name']),
            'image/png' => imageCreateFromPng($this->img['tmp_name']),
            'image/gif' => imageCreateFromGif($this->img['tmp_name']) ];

        $imgFunc = array_filter($imgFuncArr, function($func){
            return $this->img['type'] == $func;
        },ARRAY_FILTER_USE_KEY);

        $correctImg = imagecrop($imgFunc[key($imgFunc)],['x' => imageSX($imgFunc[key($imgFunc)])/2 - 320, 'y' => imageSY($imgFunc[key($imgFunc)])/2 - 120, 'width' => 640, 'height' => 240]);
        imageTtfText($correctImg, 16,0,4,230, imagecolorallocate($correctImg, 128, 128, 128), 'db/img_company/font/755.ttf', "dentalline");
        header("Content_type: image/jpeg");
        imageJpeg($correctImg, "db/img_company/{$this->wpUserId}_com.jpeg");
        imageDestroy($correctImg);
        return "img_company/{$this->wpUserId}_com.jpeg";

    }

    //подготовка загрузки изображения для Worker
    public function trainImgWorker()
    {
        $imgFuncArr = ['image/jpeg' =>imageCreateFromJpeg($this->img['tmp_name']),
            'image/png' => imageCreateFromPng($this->img['tmp_name']),
            'image/gif' => imageCreateFromGif($this->img['tmp_name']) ];

        $imgFunc = array_filter($imgFuncArr, function($func){
            return $this->img['type'] == $func;
        },ARRAY_FILTER_USE_KEY);

        $correctImg = imagecrop($imgFunc[key($imgFunc)],['x' => imageSX($imgFunc[key($imgFunc)])/2 - 120, 'y' => imageSY($imgFunc[key($imgFunc)])/2 - 120, 'width' => 240, 'height' => 240]);
        imageTtfText($correctImg, 16,0,4,230, imagecolorallocate($correctImg, 128, 128, 128), 'db/img_company/font/755.ttf', "dentalline");
        header("Content_type: image/jpeg");
        imageJpeg($correctImg, "db/img_worker/{$this->wpUserId}_worker.jpeg");
        imageDestroy($correctImg);
        return "img_worker/{$this->wpUserId}_worker.jpeg";

    }

    //запись изрбражения в бд table company
    public function setImg()
    {
        return $this->query = "UPDATE company SET company_logo = :company_logo WHERE wp_users_id = :wp_users_id";
    }

    //запись изрбражения в бд table worker
    public function setImgWorker()
    {
        return $this->query = "UPDATE workers SET img = :img WHERE wp_users_id = :wp_users_id";
    }
}