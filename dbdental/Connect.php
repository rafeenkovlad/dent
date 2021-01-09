<?php
namespace Dbdental\db;

class Connect{
    private static $pdo;

    private function __construct(){}
    
    private function __clone(){}

    private function __wakeup(){}

    public static function getConnect(){

        if(is_null(self::$pdo)){
            
            try{
                return self::$pdo = new \PDO('mysql:host=localhost;dbname=dental_info', 'root', '22172217');
            }
            catch(PDOException $e){
                echo 'Connect database failed!';
                die($e);
            }
        }
        return self::$pdo;    
    }
}

?>