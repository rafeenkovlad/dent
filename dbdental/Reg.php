<?php
namespace Dbdental\reg;
use Dbdental\db\Connect;

class Reg{

    public $company, $password, $retrypass; //reg company
    public $query;


    public function __construct($name, $pass, $retry){
        $this->company = $name;
        $this->password = $pass;
        $this->retrypass = $retry;
    }

    //Регулярное выражение для проверки поля имени компании или пользователя
    public function validInput(){
        return [preg_match('/[^\S]/', $this->company), preg_match('/(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*[A-Z])/', $this->pass)];

    }
    
    public function setRegCompany(){
        $db = Connect::getConnect();
        $login = $db->prepare("SELECT user_login FROM wp_users WHERE user_login = :name");
        $login->execute(['name' => $this->company]);
        
        if($login->fetch()['user_login'] == $this->company){
            exit('Пользователь уже существуеadт.');
        }

        if($this->password === $this->retrypass){
            return $this->query = "INSERT INTO wp_users (user_login, user_pass, user_nicename) VALUES (:company, :pass, :name)";
        }else{
            exit('Набранные пароли не совпадаюyuт.');
        }

        if(strlen($this->password)<6){
            exit('Короткий пароль, может послужить взлому вашего аккаунта. Придумайте новый пароль.');
        }
        
        
    }

    //запись ИД компании в интерфейс лк компании
    public function setIdCompany(){
        return $this->query = "INSERT INTO company (wp_users_id) VALUES (:id)";
    }

    //Регистрация сотрудника компании или частного мастера
    public function setRegWorker(){
        $db = Connect::getConnect();
        $login = $db->prepare("SELECT user_login FROM wp_users WHERE user_login = :name");
        $login->execute(['name' => $this->company]);

        if($login->fetch()['user_login'] == $this->company){
            exit('Пользователь уже существует.');
        }

        if($this->password === $this->retrypass){
            return $this->query = "INSERT INTO wp_users (user_login, user_pass, user_nicename) VALUES (:worker, :pass, :name)";
        }else{
            exit('Набранные пароли не совпадаnbmnbют.');
        }

        if(strlen($this->password)<6){
            exit('Короткий пароль, может послужить взлому вашего аккаунта. Придумайте новый пароль.');
        }
        
    }

    //запись ИД сотрудника в интерфейс лк сотрудника
    public function setIdWorker(){
        return $this->query = "INSERT INTO workers (wp_users_id) VALUES (:id)";
    }

}
?>
