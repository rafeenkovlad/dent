<?php
namespace Myprofile\write;

class Write_profile {

    public static function set_profile_reg()
    {
        add_filter( 'template_include', function(){

            load_template('wp-content/plugins/db/Form/profile/src/index.php');
        });
    }
}
