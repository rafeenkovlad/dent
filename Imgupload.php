<?php
namespace Dbdental\img;

require_once('wp-content/plugins/db/vendor/thumbs-master/Thumbs.php');
use thumbsmaster\thumbs\Thumbs;

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
    public function existsImg($name)
    {
        $adress_com = "wp-content/plugins/db/img_company/{$this->wpUserId}_com.jpeg";
        $adress_work = "wp-content/plugins/db/img_worker/{$this->wpUserId}_worker.jpeg";
        $img_com = file_exists($adress_com);
        $img_work = file_exists($adress_work);
        if($img_com and $name == 'com') unlink($adress_com);
        if($img_work and $name == 'work') unlink($adress_work);


    }

    //подготовка загрузки изображения for Company
    public function trainImg()
    {
        $imgFuncArr = ['image/jpeg' =>imageCreateFromJpeg($this->img['tmp_name']),
            'image/png' => imageCreateFromPng($this->img['tmp_name']),
            'image/gif' => imageCreateFromGif($this->img['tmp_name'])
            ];

        //удаление предыдущего изображения
        if($this->img['size'] !== 0)$this->existsImg('com');

        $imgFunc = array_filter($imgFuncArr, function($func){
            return $this->img['type'] == $func;
        },ARRAY_FILTER_USE_KEY);
        $witdh =  imageSX($imgFunc[key($imgFunc)]);
        $height = imageSY($imgFunc[key($imgFunc)]);
        $correctImg = imagecrop($imgFunc[key($imgFunc)],['x' => 0, 'y' => 0, 'width' => $witdh, 'height' => $height]);
        imageTtfText($correctImg, 16,0,4, $height-5, imagecolorallocatealpha($correctImg, 128, 128, 128, 47), 'wp-content/plugins/db/img_company/font/755.ttf', "dentaline.info");
        header("Content_type: image/jpeg");
        imageJpeg($correctImg, "wp-content/plugins/db/img_company/{$this->wpUserId}_com.jpeg");
        imageDestroy($correctImg);
        return "img_company/{$this->wpUserId}_com.jpeg";

    }

    //подготовка загрузки изображения для Worker
    public function trainImgWorker()
    {
        $imgFuncArr = ['image/jpeg' =>imageCreateFromJpeg($this->img['tmp_name']),
            'image/png' => imageCreateFromPng($this->img['tmp_name']),
            'image/gif' => imageCreateFromGif($this->img['tmp_name']) ];

        //удаление предыдущего изображения
        if($this->img['size'] !== 0)$this->existsImg('work');

        $imgFunc = array_filter($imgFuncArr, function($func){
            return $this->img['type'] == $func;
        },ARRAY_FILTER_USE_KEY);
        $witdh =  imageSX($imgFunc[key($imgFunc)]);
        $height = imageSY($imgFunc[key($imgFunc)]);
        $correctImg = imagecrop($imgFunc[key($imgFunc)],['x' => 0, 'y' => 0, 'width' => $witdh, 'height' => $height]);
        imageTtfText($correctImg, 16,0,4,$height-5, imagecolorallocatealpha($correctImg, 128, 128, 128, 47), 'wp-content/plugins/db/img_company/font/755.ttf', "dentaline.info");
        header("Content_type: image/jpeg");
        imageJpeg($correctImg, "wp-content/plugins/db/img_worker/{$this->wpUserId}_worker.jpeg");
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