<?php
$curl = curl_init('http://localhost/wordpress/?rest_route=/dental/v1/login');
curl_setopt($curl, CURLOPT_POST, 1);
$data = "login={$_REQUEST['login']}&password={$_REQUEST['password']}";
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_exec($curl);
curl_close($curl);
?>
