<?php
namespace Route\session;

class Session
{
    public function session_start()
    {
        session_start();
    }

    public function session_use($response)
    {
       return $_SESSION['token'] = get_object_vars($response)['data']['params']['token'].$_SESSION['token_refresh'] = get_object_vars($response)['data']['params']['token_refresh'];

    }

    public function session_exist()
    {

    }
}

