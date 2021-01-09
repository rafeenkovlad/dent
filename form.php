<form method = "POST" action = "">
    <input type = "file" name="csv"/>
    <input type = "submit" name = "sub" value = "сформировать"/>
</form>
<?php
function send($csv)
{
    if(isset($_POST['sub']))
    {
        var_dump($csv);
    }

}

send($_POST['csv']);
//  отправляем данные о регистрации
$_REQUEST['nameReg'] = '1zsas';
$_REQUEST['pass'] = 12345678;
$_REQUEST['retryPass'] = 12345678;
$func->dataReg($_REQUEST['nameReg'], $_REQUEST['pass'], $_REQUEST['retryPass']);
$func->regCompany($db);
print_r($db->lastInsertId());