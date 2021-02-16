<?php
namespace Coderun\ContentCabinet;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\ValidationData;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Hmac\Sha512;


/**
 * Class AuthJwt
 * Документация https://github.com/lcobucci/jwt/blob/3.3/README.md
 * @package Coderun\ContentCabinet
 */
class AuthJwt{

    protected $secret_key='megaKey1238';

    public function __construct() {
    }
    //Создаём токен
    public function create($id) {
        $time = time();

        $signer = new Sha512();

        $token = (new Builder())
            ->issuedBy('/')
            ->permittedFor('/')
            ->identifiedBy(md5("user_id_{$id}"), true)
            ->issuedAt($time)
            ->canOnlyBeUsedAfter($time + 60)
            ->expiresAt($time + 129600)
            ->withClaim('userId', $id)
            ->getToken($signer, new Key($this->secret_key));

        $token->getHeaders();

        $token->getClaims();

        return (string)$token;
    }
    //Создаем рефреш
    public function createRefresh($id) {
        $time = time();

        $signer = new Sha512();

        $token = (new Builder())
            ->issuedBy('/')
            ->permittedFor('/')
            ->identifiedBy(md5("user_id_{$id}"), true)
            ->issuedAt($time)
            ->canOnlyBeUsedAfter($time + 60)
            ->expiresAt($time + 120)
            ->withClaim('userId', $id)
            ->getToken($signer, new Key($this->secret_key));

        $token->getHeaders();

        $token->getClaims();

        return (string)$token;
    }
    //Проверка токена
    public function validate(string $token_check):array {

        $result=[];
        $signer = new Sha512();


        try {
            $token = (new Parser())->parse($token_check);

            $id=$token->getClaim('userId');

            $data = new ValidationData();
            $data->setIssuer('/');
            $data->setAudience('/');
            $data->setId(md5("user_id_{$id}"));
            $data->setCurrentTime(time()+61);

            $result['userId']=$id;
            $result['isValid']=$token->verify($signer,$this->secret_key);

            if($result['isValid']) {
                $result['isValid']=!$token->isExpired();
            }

            return $result;

        }catch (\Exception $e) {
            return $result;
        }

    }
//Обновление данных в таблице
    public function addTable(array $field) {
        global $wpdb;
        $table_name='coderun_content_cabint_jwt';

        $wpdb->delete($table_name,['user_id'=>$field['user_id']],['%d']);

        $default_field=[
            'user_id'=>0,
            'auth_token'=>'',
            'refresh_token'=>'',
        ];

        $field=array_merge($default_field,$field);

        $wpdb->insert($table_name,$field,['%d','%s','%s']);

    }

    public function getUserIdToRefreshToken($refresh_token):int {
        global $wpdb;
        $table_name='coderun_content_cabint_jwt';

        $data=$wpdb->get_row("select user_id from {$table_name} where refresh_token='{$refresh_token}'");

        if(empty($data)) {
            return 0;
        }
        return (int)$data->user_id;
    }
    public function getUserIdToToken($token):int {
        global $wpdb;
        $table_name='coderun_content_cabint_jwt';

        $data=$wpdb->get_row("select user_id from {$table_name} where auth_token='{$token}'");

        if(empty($data)) {
            return 0;
        }
        return (int)$data->user_id;
    }

}
