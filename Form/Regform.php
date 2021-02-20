<?php
namespace Form\regform;

class Regform
{
    public function get_reg_form()
    {
        add_shortcode('reg_form', array(&$this, 'reg_form'));
    }

    public function reg_form($atts, $content=null)
    {
        $content = require_once('reg/index.html');
        return $content[0];
    }
}

