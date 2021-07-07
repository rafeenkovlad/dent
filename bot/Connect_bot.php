<?php
 namespace Bot\Config;

 require_once('../dbdental/Connect.php');
 require_once('../dbdental/Snsearch.php');
 use Dbdental\Snsearch;
 use Dbdental\db\Connect;

 class Connect_bot
 {
     private $data, $db, $query;
     private static $token;
     public function __construct($connectDb = Connect::class)
     {
         $this->data  = file_get_contents('php://input');
         //$this->data = json_decode($data, true);
         $this->db = $connectDb::getConnect();
         self::get_token();
         $this->commands();
         $this->prepare();
     }

     private static function get_token()
     {
         self::$token = '1809961465:AAEeYmPU0ttCPlPK8z9gTX7OeLd6CZtjTwM';
     }

     private function commands()
     {
         preg_match('{(?<="text":") (/sn|/sn\s[0-9a-z-_/\s]+) (?=")}xmi', $this->data, $command);
         if($command[0] == '/sn'){
             $this->data = json_decode($this->data, true);
             file_put_contents(__DIR__ . '/message.txt', print_r($command, true));

             $response = array(
                 'chat_id' => $this->data['message']['chat']['id'],
                 'text' => $text = 'Начать поиск можно так: /sn[пробел]серийный номер'
             );

             $ch = curl_init('https://api.telegram.org/bot' . self::$token . '/sendMessage');
             curl_setopt($ch, CURLOPT_POST, 1);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch, CURLOPT_HEADER, false);
             curl_exec($ch);
             curl_close($ch);

             $this->data =['bot' => $text];
         }
         if(mb_strlen($command[0])>3 ){
             preg_match('{(?<=/sn\s)[0-9a-z-_/\s]+}xsi',$command[1], $sn );
             $search = new Snsearch($this->db);
             $text = $search->search($sn[0]);
             file_put_contents(__DIR__ . '/message.txt', print_r($text, true));

             $this->data = json_decode($this->data, true);
             $response = array(
                 'chat_id' => $this->data['message']['chat']['id'],
                 'text' => $text,
                 'parse_mode' => 'HTML'
             );

             $ch = curl_init('https://api.telegram.org/bot' . self::$token . '/sendMessage');
             curl_setopt($ch, CURLOPT_POST, 1);
             curl_setopt($ch, CURLOPT_POSTFIELDS, $response);
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch, CURLOPT_HEADER, false);
             curl_exec($ch);
             curl_close($ch);

             $this->data =['bot' => $text];
         }

     }

     private function prepare()
     {

         preg_match('/,"(photo|document|voice|video_note)":/xmi', $this->data, $result);
         if(sizeof($result)<1):
             $this->prepare_non_file();
         else:
             $this->data = json_decode($this->data, true);
         endif;

         if($result[1] == 'photo') $photo = array_pop($this->data['message']['photo']);
         if($result[1] == 'document') $document = $this->data['message']['document']['file_id'];
         if($result[1] == 'voice') $voice = $this->data['message']['voice']['file_id'];
         if($result[1] == 'video_note') {
             $this->data['message']['text'] = "<a href ='#'>Содержит видео</a>".$this->data['message']['caption']??null;
             unset($this->data['message']['video_note']);
             unset($this->data['message']['caption']);
             $this->data = json_encode($this->data);

             $this->prepare_non_file();
         }


         if(isset($photo) || isset($document) || isset($voice))
         {
             $ch = curl_init('https://api.telegram.org/bot' . self::$token . '/getFile');
             curl_setopt($ch, CURLOPT_POST, 1);
             curl_setopt($ch, CURLOPT_POSTFIELDS, array('file_id' => $photo['file_id'] ?? $document ?? $voice ));
             curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
             curl_setopt($ch, CURLOPT_HEADER, false);
             $res = curl_exec($ch);
             curl_close($ch);

             $res = json_decode($res, true);
         }

         if ($res['ok']) {
             $src  = 'https://api.telegram.org/file/bot' . self::$token . '/' . $res['result']['file_path'];
             $filename = time() . '-' . basename($src);
             $dest = __DIR__ . '/file/' . $filename;
             copy($src, $dest);
             $this->data['message']['file'] = $filename;

             $this->data['message']['text'] = $this->data['message']['caption']??null;
             unset($this->data['message']['photo']);
             unset($this->data['message']['document']);
             unset($this->data['message']['caption']);
             unset($this->data['message']['voice']);

             $this->data = json_encode($this->data);

             $this->prepare_non_file();
         }

     }

     private function prepare_non_file()
     {
         if(is_array($this->data))$this->data = json_encode($this->data);
         $connect = $this->db->prepare($this->query = "INSERT INTO telegram_group (message) VALUES (:message)");
         $connect->execute(['message' => $this->data]);


     }
 }

 $start = new Connect_bot();





