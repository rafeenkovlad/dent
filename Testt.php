<?php
namespace Test;
use Dbdental\reg\Reg;

class Testt{
    function test(){
        $new = new Reg('test', '1', '1');
        $new->setRegCompany();
    }
}
