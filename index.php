
<?php
require_once 'vendor/autoload.php';
use Dbdental\db\Connect;
use FunctionCommand\Functions;


$db = Connect::getConnect();
$func = new Functions();

//  отправляем данные о регистрации
$_REQUEST['nameReg'] = 'vladvlad';
$_REQUEST['pass'] = '!12345678Az';
$_REQUEST['retryPass'] = '!12345678Az';
$func->dataReg($_REQUEST['nameReg'], $_REQUEST['pass'], $_REQUEST['retryPass']);
$func->regWorker($db);
print_r($db->lastInsertId());

/*//Запись данных о сотруднике
$func->dataWorker(['name', 'dolgnost', 'contacti' , 76 ]);
$func->workerSet($db);

//вывод данных компании
print_r($func->companyInfo($db, get_current_user_id())['name_company']);

//запись данных о компании
$func->dataCompany(['onedentук', 'test', 'test' ,'test', 73 ]);
$func->companySet($db); */

//запись email компании
$func->dataEmail('email@test.ru');
$func->companyEmail($db, '76');

//отправляем товарный лист компании
echo
'<form method = "POST" action = "" enctype = "multipart/form-data">
    <input type = "file" name="csv"/>
    <input type = "submit" name = "sub" value = "сформировать"/>
</form>';
if(isset($_POST['sub'])) {
    $func->sendCSV($db, $_FILES['csv']['tmp_name'], get_current_user_id());
}

//загрузка изображения компании
echo
'<form method = "POST" action = "" enctype = "multipart/form-data">
    <input type = "file" name="img"/>
    <input type = "submit" name = "sub_img" value = "upload"/>
</form>';
if(isset($_POST['sub_img'])) {
    $func->setImgCompany($db, $_FILES['img'], get_current_user_id());
}

//записываем компанию, поставившую лайк
$objAuthor = get_user_by('login', get_the_author());//получаем объект автора поста
$objUser = get_user_by('ID', get_current_user_id());//получаем объект пользователя
$func->likeData(get_the_ID(), $objAuthor->ID, [$objUser->ID => $objUser->user_login]);
$func->setRait($db);
//получаем лайки
$func->likeUser($db);
var_dump($func::$likeArr);

//Отправка данных о незаренитрированном посте

$func->postArr('aaaa', 'bbbb', '123123', 'ewrefsf', 'qweqwr');
$func->nonAuthPost($db);

//считаем общее количество лайков автора
$like = $func->likeAuth($db, get_current_user_id());

//Создаем чат между пользователями
//$func->newChat($db, get_current_user_id(), get_the_ID());
//получаем список открытых чатов
$func->listChat($db, get_current_user_id());
//Входим в чат комнату
$func->inChat($db, get_current_user_id(), $_REQUEST['room_id'], $_REQUEST['subject']);
