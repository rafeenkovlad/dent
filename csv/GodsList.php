<?php
namespace ListCSV;


class GodsList
{
    public $dataList, $CSV, $dataArr;
    private static $query, $issetQuery, $update, $delete;

    public function getGods($CSV)
    {
        $this->CSV = $CSV;
        $this->dataList = fopen($this->CSV, "rt") or die('произошла ошибка! обратитесь к администратору.');


        for($i = 0; $data = fgetcsv($this->dataList, 4000, ','); $i++)
        {
            [$name, $sirial_numb, $madeIn, $cost, $litleInfo, $image] = $data;
            $this->dataArr[] = ['name' => $name, 'sirial_numb' => $sirial_numb, 'made_in_company' => $madeIn, 'price' => $cost, 'litle_info' => $litleInfo, 'image' => $image];

        }
        return $this->dataArr;

    }

    public static function queryCSV()
    {
        return self::$query = "INSERT INTO excel_list (name, sirial_number, made_in_company, price, litle_info, company_id, img_url) VALUES (:name, :sirial_number, :made_in_company, :price, :litle_info, :company_id, :img_url);";

    }

    //проверка колличества строк прайс листа компании
    public static function issetCSV()
    {
        return self::$issetQuery = "SELECT id FROM excel_list WHERE company_id = :company_id";
    }

    //Обновление прайс листа компании
    public static function updateCSV()
    {
        return self::$update = "UPDATE excel_list SET name = :name, sirial_number = :sirial_number, made_in_company = :made_in_company, price = :price, litle_info = :litle_info, img_url = :img_url WHERE id = :id";
    }

    //Удалить прайс компании для того, что бы записать новый прайс с большим количеством полей
    public static function deleteCSV()
    {
        return self::$delete = "DELETE FROM excel_list WHERE company_id = :company_id";
    }

    public function succesCsvResponse($n_strok)
    {
        echo "<script type='text/javascript'> alert('было успешно сохранено {$n_strok} строк(и).'); </script>";
    }
}

