<?php
namespace Dbdental\userinfo;
use Mihanentalpo\FastFuzzySearch\FastFuzzySearch;

class Userinfo{
    private static $query;


    public function getAllNames()
    {
        return self::$query = "SELECT name_company, wp_users_id FROM company UNION SELECT full_name, wp_users_id FROM workers";
    }

    public function search($arr,$input)
    {
        $unArr = array_map(function($arr)
        {
            $arr = [$arr['wp_users_id'] => $arr['name_company']];
            return $arr;
        },$arr);

        foreach ($unArr as $value)
        {
            foreach ($value as $key => $newvalue) {
                $un[$key] = $newvalue;
            }
        }

        $ffs = new FastFuzzySearch($un);
        $results = $ffs->find($input,3);
        return array_map(function ($result) use ($un)
        {
            $id = array_search($result['word'], $un);
            return ( $un[$id] == $result['word'] ) ? [$id => $result['word']] : print_r('Ничего не найдено...') ;

        }, $results);

    }

    public function end($arr)
    {
        foreach($arr as $val)
        {
            foreach($val as $key => $value)
            {
                print_r("{$key} . {$value}");
            }
        }
    }


}