<?php
namespace Dbdental\company;

class CompanyInfo
{
    public $array, $arrayCompany, $id;
    public $query;
    private $arrayKey;

    public function setInfoCompany($dataCompany){
        $this->array = $dataCompany;
        $this->arrayKey = ['name_company', 'group_gods', 'contact', 'info', 'id'];
        //подсчет коллекции и присваивание ключ=валуе
        for($i=0;$i<sizeof($this->array);$i++){
            $this->arrayCompany[$this->arrayKey[$i]] = $this->array[$i];
        }
        var_dump($this->arrayCompany);
        return $this->query = "UPDATE company SET name_company = :nameCompany, group_gods = :gods, contact = :contact, info = :info WHERE id = :id";
    }

    //запись почты в главную таблицу вп_юсерс
    public function setEmailCompany($email, $id){
        $this->id = $id;
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            var_dump('ok');
            return $this->query = "UPDATE wp_users SET user_email = :email WHERE ID = :id";
        }
    }

    public function getInfoCompany($id){
        $this->id = $id;
        return $this->query = "SELECT * FROM company WHERE id = :id";
    }

}
?>