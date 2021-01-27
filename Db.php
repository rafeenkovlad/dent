<?php
/*
 * Plugin Name: dentaline
 * Author: Vladislav Rafeenko <rafeenkovlad@gmail.com>
 * Version: 0.1
 */
require_once 'vendor/autoload.php';
use Dbdental\db\Connect;
use FunctionCommand\Functions;


class Db{

    private $db;
    public $func;

    public function db()
    {
        $this->db = Connect::getConnect();
    }

    public function func()
    {
        $this->func = new Functions();
    }

    //  отправляем данные о регистрации
    public function setReg($_REQUEST['nameReg'], $_REQUEST['pass'], $_REQUEST['retryPass'])
    {
        $this->func()->dataReg($_REQUEST['nameReg'], $_REQUEST['pass'], $_REQUEST['retryPass']);
        $this->func()->regCompany($this->db());
        print_r($this->db()->lastInsertId());
    }
}

$db = new Db();
$db->setReg('wqe','123','123');
?>

