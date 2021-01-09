<?php
require_once 'vendor/autoload.php';
use FunctionCommand\Functions;
use Dbdental\db\Connect;

$db = Connect::getConnect();
$func = new Functions();
$func->chating($_POST['text_chat'], $_POST['id'], $db);
