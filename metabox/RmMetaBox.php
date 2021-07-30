<?php
namespace Metabox;

class RmMetaBox{

    public static function hueman_post_options_remoove()
    {
        add_action( 'add_meta_boxes' , function(){
            remove_meta_box( 'post-options' , 'post' , 'normal' );
        }, 99 );
    }
}