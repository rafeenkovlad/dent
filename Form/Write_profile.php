<?php
namespace Myprofile\write;

class Write_profile {

    public static function set_profile_reg()
    {
        add_filter( 'template_include', function(){

            require_once ('profile/src/index.php');
        });
    }
}
