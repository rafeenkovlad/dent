<?php
namespace Myprofile\write;

class Write_profile {

    public static function set_profile_reg()
    {
        add_filter( 'template_include', function(){

            return wp_normalize_path( WP_PLUGIN_DIR ) . '/db/Form/profile.php';
        });
    }
}
