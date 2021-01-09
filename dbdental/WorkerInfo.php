<?php
namespace Dbdental\worker;

class WorkerInfo
{
    public $array, $arrayWorker, $id;
    public $query;
    private $arrayKey;

    public function setInfoWorker($dataWorker){
        $this->array = $dataWorker;
        $this->arrayKey = ['full_name', 'dolgnost', 'contacts', 'id'];
        //подсчет коллекции и присваивание ключ=валуе
        for($i=0;$i<sizeof($this->array);$i++){
            $this->arrayWorker[$this->arrayKey[$i]] = $this->array[$i];
        }
        var_dump($this->arrayWorker);
        return $this->query = "UPDATE workers SET full_name = :nameWorker, dolgnost = :dolgnost, contacts = :contacts WHERE wp_users_id = :id";
    }

    //запись почты в главную таблицу вп_юсерс
    public function setEmailWorker($email, $id){
        $this->id = $id;
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            var_dump('ok');
            return $this->query = "UPDATE wp_users SET user_email = :email WHERE ID = :id";
        }
    }

    public function getInfoWorker($id){
        $this->id = $id;
        return $this->query = "SELECT * FROM workers WHERE id = :id";
    }
}