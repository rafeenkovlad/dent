<?php
namespace FunctionCommand;
use Dbdental\reg\Reg;
use Dbdental\company\CompanyInfo;
use Dbdental\worker\WorkerInfo;
use ListCSV\GodsList;
use Dbdental\like\Rait;
use Dbdental\img\Imgupload;
use Outpost\HelpPost;
use Author\rait\Authrait;
use Dbdental\chat\Chat;

class Functions
{
    //свойства для регистрации
    public $nameReg, $pass, $retryPass;

    //коллекция свойств для записи данных о компании/ сотрудника
    public $dataCompany, $email, $dataWorker;

    //Данные для понравившихся постов
    public $idPost, $idUser, $idUserLike, $name;

// Принимаем регистрационные данные
    public function dataReg($nameReg, $pass, $retryPass)
    {
        $this->nameReg = $nameReg;
        $this->pass = $pass;
        $this->retryPass = $retryPass;
    }

//регистрация компании
    public function regCompany($db)
    {
        $reg = new Reg($this->nameReg, $this->pass, $this->retryPass);//компаия / пароль / подтверждение
        $ver = $db->prepare($reg->setRegCompany());
        var_dump($reg->company);
        $ver->execute(['company' => $reg->company, 'pass' => md5($reg->password), 'name' => $reg->company]);  //вставка регистрационных данных компании в бд
        $insertId = $db->prepare($reg->setIdCompany());
        $insertId->execute(['id' => $db->lastInsertId()]);
    }

//получить данные компании
    public function companyInfo($db, $id)
    {
        $company = new CompanyInfo();
        $getCompany = $db->prepare($company->getInfoCompany($id));
        $getCompany->execute(['id' => $company->id]);// получаем данные компании в коллекцию
        return $getCompany->fetch();
    }

//Принимаем данные о компании
    public function dataCompany(array $dataCompany)
    {
        $this->dataCompany = $dataCompany;
    }

//Записать новые данные о компании
    public function companySet($db)
    {
        $company = new CompanyInfo();
        $setCompany = $db->prepare($company->setInfoCompany($this->dataCompany));//тут должны быть array переменнx
        $setCompany->execute(['nameCompany' => $company->arrayCompany['name_company'], 'gods' => $company->arrayCompany['group_gods'],
            'contact' => $company->arrayCompany['contact'], 'info' => $company->arrayCompany['info'], 'id' => $company->arrayCompany['id']]);
    }

//принимаем email  компании
    public function dataEmail($email)
    {
       $this->email = $email;
    }

//запись электронного адресса компании
    public function companyEmail($db, $id)
    {
        if(preg_match('/^[^\s*] ([a-zA-Z0-9_.])+ @ ([a-zA-Z0-9])+ [.] ([a-zA-Z0-9])+$/xsi', $this->email)) {
            $company = new CompanyInfo();
            $setEmail = $db->prepare($company->setEmailCompany($this->email, $id));
            $setEmail->execute(['email' => $this->email, 'id' => $company->id]);
        }
    }

//загрузка изображения компании
    public function setImgCompany($db, $imgCompany, $wpUserId)
    {
        $img = new Imgupload($imgCompany, $wpUserId);
        $setUrl = $db->prepare($img->setImg());
        $setUrl->execute(['company_logo' => $img->trainImg(), 'wp_users_id' => $img->wpUserId]);
    }

//Подготовка записи прайс листа компании
    public function sendCSV($db, $csv, $wpUserId)
    {
        $list = new GodsList();
        $isset = $db->prepare($list::issetCSV());
        $isset->execute(['company_id' => $wpUserId]);

        //создаем массив со значениями из массива pdo
        $bdListId = array_values($isset->fetchAll(\PDO::FETCH_ASSOC));
        $arrCSV =& $list->getGods($csv);

        if(sizeof($bdListId) > sizeof($arrCSV))
        {
            //обновляем прайс если строк в таблице больше чем в файле
            $i = 0;
            $update = $db->prepare($list::updateCSV());
            foreach($arrCSV as $thisArr){
                $update->execute(['name' => $thisArr['name'], 'sirial_number' => $thisArr['sirial_numb'],
                    'made_in_company' => $thisArr['made_in_company'], 'price' => $thisArr['price'],
                    'litle_info' => $thisArr['litle_info'], 'id' => $bdListId[$i++]['id']]);
            }


        }else{
            echo 'menshe';
            //удаляем прайс компании для того что бы записать новый с большим количеством наименований
            $delete = $db->prepare($list::deleteCSV());
            if($delete->execute(['company_id' => $wpUserId]))
            {
                $setCSV = $db->prepare($list::queryCSV());
                foreach($arrCSV as $thisArr) {
                    $setCSV->execute(['name' => $thisArr['name'], 'sirial_number' => $thisArr['sirial_numb'],
                        'made_in_company' => $thisArr['made_in_company'], 'price' => $thisArr['price'],
                        'litle_info' => $thisArr['litle_info'], 'company_id' => $wpUserId]);
                }
            }

        }

    }

//регистрация сотрудника компании
    public function regWorker($db)
    {
        $reg = new Reg($this->nameReg, $this->pass, $this->retryPass);//сотрудник / пароль / подтверждение
        print_r($reg->validInput());//проверка регистрационных данных
        $ver = $db->prepare($reg->setRegWorker());
        $ver->execute(['worker' => $reg->company, 'pass' => md5($reg->password), 'name' => $reg->company]); //вставка регистрационных данных компании в бд
        $insertId = $db->prepare($reg->setIdWorker());
        $insertId->execute(['id' => $db->lastInsertId()]);
    }

//Принимаем данные о сотрудике
    public function dataWorker(array $dataWorker)
    {
        $this->dataWorker= $dataWorker;
    }

//Записать новые данные о сотруднике
    public function workerSet($db)
    {
        $worker = new WorkerInfo();
        $setWorker = $db->prepare($worker->setInfoWorker($this->dataWorker));//тут должны быть array переменнx
        $setWorker->execute(['nameWorker' => $worker->arrayWorker['full_name'], 'dolgnost' => $worker->arrayWorker['dolgnost'],
            'contacts' => $worker->arrayWorker['contacts'], 'id' => $worker->arrayWorker['id']]);
    }

//загрузка изображения сотрудника
    public function setImgWorker($db, $imgCompany, $wpUserId)
    {
        $img = new Imgupload($imgCompany, $wpUserId);
        $setUrl = $db->prepare($img->setImgWorker());
        $setUrl->execute(['img' => $img->trainImgWorker(), 'wp_users_id' => $img->wpUserId]);
    }


//Принимаем данные о понравившейся записи
    public function likeData( $idPost, $idUser, $idUserLike)
    {
        $this->idPost = $idPost;
        $this->idUser = $idUser;
        $this->idUserLike = $idUserLike;
    }

//Оценка поста другими компаниями и работниками
    public function setRait($db)
    {
        $rait = new Rait();

        $getIssetPosts = $db->prepare($rait->oneLike());
        $getIssetPosts->execute(['id' => $this->idPost]);
        $wpPosts =& $getIssetPosts->fetchAll();

        if(!empty($wpPosts))
        {
            foreach($wpPosts as $value){

                $nowArr = $value;
            }
            $arr =& json_decode($nowArr['like_array'], TRUE);
            if(!array_key_exists(key($this->idUserLike), $arr)){

                $likeArr = $arr + $this->idUserLike;
                $setLikeUser = $db->prepare($rait->raitSetLikeUser());
                $setLikeUser->execute(['like_array' => json_encode($likeArr), 'wp_posts_id' => $nowArr['wp_posts_id']]);
            }

        }else{


            $getNameCompany = $db->prepare($rait->getNameCompany());
            $getNameCompany->execute(['id' => $this->idUser]);
            $this->name =& $getNameCompany->fetch()['name_company'];

            if (strlen($this->name) == 0) {
                $getNameWorker = $db->prepare($rait->getNameWorker());
                $getNameWorker->execute(['id' => $this->idUser]);
                $this->name =& $getNameWorker->fetch()['full_name'];
            }


            $setLike = $db->prepare($rait->raitSetLike());

            if (!$setLike->execute(['name_co_and_wo' => $this->name, 'wp_users_id' => $this->idUser, 'like_array' => json_encode($this->idUserLike), 'wp_posts_id' => $this->idPost])) {
                print_r($setLike->errorInfo());
            }
        }
    }

//Получить все лайки поста
    public static $likeArr;
    public function likeUser($db)
    {
        $rait = new Rait();

        $getLike = $db->prepare($rait->getLike());
        $getLike->execute(['wp_post_id' => $this->idPost]);
        $jsonArr = $getLike->fetch()['like_array'];
        self::$likeArr =& json_decode($jsonArr, TRUE);
    }

//Запись поста о помощи незарегистрированными пользователями
    public $postArr;
    public function postArr(...$arr)
    {
        $tmpArr = ['text' => $arr[0], 'email' => $arr[1], 'info_query_company' => $arr[2],'meta' =>$arr[3], 'contacts' => $arr[4]];
        $this->postArr = $tmpArr;
    }
    public function nonAuthPost($db)
    {
        $post = new HelpPost();
        $wpPost = $db->prepare($post->setPost());
        $wpPost->execute(['text' => $this->postArr['text'], 'meta_name' => $this->postArr['meta'], 'post_date' => date("Y-m-d")]);
        $postHelp = $db->prepare($post->setPostInfo());
        $postHelp->execute([
            'wp_posts_id' => $db->lastInsertId(),
            'email' => $this->postArr['email'], 'info_query_company' => $this->postArr['info_query_company'], 'meta' => $this->postArr['meta'], 'contacts' => $this->postArr['contacts']
        ]);


    }

//Считаем общее колличество лайков по записям принадлежащих автору
    public function likeAuth($db, $id)
    {
        $rait = new Authrait($id);
        $likeArr = $db->prepare($rait->getArrLike());
        $likeArr->execute(['id' => $rait->id]);
        $count = $rait->getCount(array_merge(...$likeArr->fetchAll(\PDO::FETCH_NUM)));
        $setLike = $db->prepare($rait->userExists($db));
        $setLike->execute(['id' => $id, 'count' => $count]);

    }

    //Создание чата между пользователями
    public function newChat($db, $id, $idPost)
    {
        $chat = new Chat($id);
        $getUser = $db->prepare($chat->getUserIdChat());
        $getUser->execute(['ID' => $idPost]);
        //вставляем данные о пользователях состоящих в чате
        $chatSucces = $db->prepare($chat->createChat($getUser->fetch(\PDO::FETCH_ASSOC)['post_author']));
        $chatSucces->execute();

    }

    //Просмотр страницы с открытыми чатами
    public function listChat($db, $id)
    {
        $chat = new Chat($id);
        //получаем массив id_user созданого чата
        $getId = $db->query($chat->getArrId());
        //получаем массив в каких чатах состоит пользователь
        $listChat = $chat->myChat($getId->fetchAll(\PDO::FETCH_ASSOC), $id);
        //Добаваляем кнопку с индификатором для входа в чат
        $chat->buttonRoom($listChat);

    }

    //Входим в чат комнату для просмотра содержимого переписки
    public function inChat($db, $id, $idChatRoom, $subject)
    {
        $chat = new Chat($id);
        //проверка существует ли тема чата
        $chatSubExist = $db->prepare($chat->subjectDbValid());
        $chatSubExist->execute(['id' => $idChatRoom]);
        //проверка темы чата
        $validateSub = $db->prepare($chat->validateSubject($subject));
        $issetBdSet = $validateSub->execute(['id' => $idChatRoom]);
        if($issetBdSet or isset($chatSubExist->fetch(\PDO::FETCH_ASSOC)['subject']))
        {
            //вход в чат комнату
            $inChatRoom = $db->prepare($chat->inChatRoom());
            $inChatRoom->execute(['id' => $idChatRoom]);
        } else {
           print_r("Тема не доджна быть пустой!*Тема не должна начинаться с пробельного символа.");
        }
    }

    //Ведем общение в чате через файл приемник .chatexist.php используя ajax -> chatexist -> functions -> chat
    public function chating($text, $idArr, $db)
    {
        $id = json_decode($idArr);
        var_dump($idArr);
        $chat = new Chat($id->id);
        $send = $db->prepare($chat->sendMssg($text));
        $send->execute(['id_chat' => $id->idChat]);
        //получить имя
        $getName = $db->prepare($chat->nameForArr());
        $getName->execute(['id' => $id->id]);
        //получаем результат
        $get = $db->prepare($chat->getMssg());
        $get->execute(['id' => $id->idChat]);
        $textStr = $get->fetch(\PDO::FETCH_ASSOC)['text_array'];
        $textJson = '[' . preg_replace('/}{/xm', '},{', $textStr) . ']';
        $chat->arrMssg($textJson, $getName->fetch(\PDO::FETCH_NUM));
    }
}



?>