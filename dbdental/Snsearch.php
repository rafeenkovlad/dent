<?php
namespace Dbdental;

require_once ('../vendor/mihanentalpo/fast-fuzzy-search/src/FastFuzzySearch.php');
use Mihanentalpo\FastFuzzySearch\FastFuzzySearch;

class Snsearch
{
    private $query;
    private static $db;


    public function __construct($db)
    {
        self::$db = $db;
        $query = "SELECT sirial_number FROM excel_list";
        $query = self::$db->query($query);
        $this->query = $query->fetchAll(\PDO::FETCH_FUNC, [&$this, 'arrRestrict']);
    }

    public function search($input)
    {
        $ffs = new FastFuzzySearch($this->query);
        $snSearch = $ffs->find($input,3);
        $query = "SELECT name, sirial_number, company_id FROM excel_list WHERE sirial_number = :sirial_number";
        $query = self::$db->prepare($query);
        foreach($snSearch as $valid)
        {
            $query->execute(['sirial_number' => $valid['word']]);
            $arr[] = ['sn' => $valid, 'response' => $query->fetchALL(\PDO::FETCH_FUNC, [&$this, 'addUrl'])];
        }

        return $this->editResponseTelegramGroup($arr);


    }

    public function arrRestrict($sirial_number)
    {
        return $sirial_number;
    }

    public function addUrl($name, $sirial_number, $company_id)
    {
        return "<a href ='https://d993e4fea038.ngrok.io/dental2/wordpress/wp-json/dental/v1/get-telegram-url?id_user={$company_id}&sn={$sirial_number}'> {$name} </a> : {$sirial_number}";
    }

    private function editResponseTelegramGroup($arr)
    {
        foreach($arr as $build)
        {
            $percent = $build['sn']['percent'] * 100;
            $str = $build['sn']['word']." совпадение: {$percent}%\n";
            foreach($build['response'] as $url)
            {
                $str.=  "{$url}\n";
            }
            $endArr[] =$str;
        }
        return (empty($arr))? "Серийный номер не найден" : "{$endArr[0]}\n{$endArr[1]}\n{$endArr[2]}";
    }
}