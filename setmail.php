<?php
namespace Mail\settings;

function includMail()
{
    // Create the Transport
    $transport = (new Swift_SmtpTransport('smtp-relay.gmail.com', 465))
        ->setUsername('имя_пользователя_без_знака_@_и_домена')
        ->setPassword('пароль_пользователя')
        ->setEncryption('SSL')
    ;

// Create the Mailer using your created Transport
    $mailer = new Swift_Mailer($transport);
}

/*
 * https://blog.lisogorsky.ru/send-mail-by-smtp настройки необходим аккаунт администратора iclod
 * */