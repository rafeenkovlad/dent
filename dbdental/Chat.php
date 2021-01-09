<?php

namespace Dbdental\chat;

class Chat
{
    private static $time, $id, $idUser;
    private static $query;
    public static $getIdUser;

    public function __construct($id)
    {
        self::$id = $id;
    }

    public function createChat($idUser)
    {
        self::$idUser = (int) $idUser;
        return self::$query = "INSERT INTO chat (id_user) VALUES ('".json_encode([self::$id, self::$idUser])."')";
    }
    //Получаем ид пользователя на его странице
    public function getUserIdChat()
    {
        return self::$query= "SELECT post_author FROM wp_posts WHERE ID = :ID";
    }

    //получаем массив ид пользователей
    public function getArrId()
    {
        return self::$query = "SELECT id, id_user, subject FROM chat ORDER BY id DESC ";
    }

    //определяем в каких чатах состоит пользователь
    public function myChat($arr, $id)
    {
        $chatId = array_filter($arr, function ($n) use ($id)
        {
            foreach (json_decode($n['id_user']) as $nId)
            {
               return ($nId == $id) ? true : false;
            }
        });
        return $chatId;
    }

    //Показываем мои чаты с кнопкой для входа в чат
    public function buttonRoom($listChat)
    {
        foreach ($listChat as ['id' => $room, 'subject' => $subject])
        {
            $inputSubject = (!empty($subject)) ? $subject : "<input type = 'text' name = 'subject' value = '' />";
            echo "
                    <form method = 'POST' action = ''>
                    {$inputSubject}
                    <input type ='hidden' name = 'room_id' value = {$room} />
                    <button type='submit' name = 'id_chat' value =''>{$subject}</button>
                    </form>
                    ";
        }
    }

    //Проверка существует уже тема чета или нет
    public function subjectDbValid()
    {
        return self::$query = "SELECT subject FROM chat WHERE id = :id";
    }
    //проверка темы чата
    public function validateSubject($subject)
    {
        if (preg_match('/^(?=[\S+\w+])/iusx', $subject))
        {
            return self::$query = "UPDATE chat SET subject = '".$subject."' WHERE id = :id";
        }
    }

    //показать чат
    public function inChatRoom()
    {
        //форма обшения через чат
        if(!empty($_REQUEST['room_id']))
        {
            echo
                '<form method = "POST" action = "">
    <input type = "hidden" id = "idArr" value = '.json_encode(["id" => get_current_user_id(), "idChat" => $_REQUEST['room_id']]).' />
    <textarea type = "text" id = "text_chat" rows = "5"></textarea>
    <button type = "submit" id = "send_text"> Отправить </button>
    </form>
    <script type ="text/javascript">
    jQuery(function($){
        $(document).ready(function(){
            $("#send_text").on("click", function(){
                if($.trim($("#text_chat").val()) === "")
                    {
                        alert("Сообщение пустое!");
                        return false;                  
                    }
                //блокировка кнопки на время отправки
                $("#send_text").prop("disabled", true);
                $.ajax({
                url: "db/chatexist.php",
                method: "POST",
                data: {text_chat: $("#text_chat").val(),
                       id: $("#idArr").val()}
                }).done(function(data){
                    //получение ответа
                    $("#result").html(data);
                    $("#send_text").prop("disabled", false);
                })
            })
        })
    })
    </script>
    <div id = "result"></div>';
        }
        //показать чат
        return self::$query = "SELECT text_array FROM chat WHERE id = :id; ";
    }
    //отправляем сообщение
    public function sendMssg($text)
    {

        return self::$query = "UPDATE chat SET text_array = CONCAT(text_array, '".json_encode(['id' => self::$id, 'text' => preg_replace('/(\\\)/mxi', '&#92', str_replace(["\r\n", "\r", "\n"],"<br>",$text))], JSON_UNESCAPED_UNICODE)."') WHERE id = :id_chat";
    }
    //Получаем ответ
    public function getMssg()
    {
        return self::$query = "SELECT text_array FROM chat WHERE id = :id";
    }
    //определить имя
    public function nameForArr()
    {
        return self::$query = "SELECT name_company FROM company WHERE wp_users_id = :id UNION SELECT full_name FROM workers WHERE wp_users_id = :id";
    }
    //Выводим результат
    public function arrMssg($textJson, $name)
    {
        $msgArr = array_map(function($msg)use($name)
        {
            return ['id' => $msg['id'], 'name' => $name[0], 'text' => $msg['text']];
        }, json_decode($textJson, true));
        var_dump($msgArr);
    }

}

?>