<?php
namespace Form\alert;

class Alert
{
    public static function doactionAlert($alert, $name)
    {

        add_action('alert_red', [Alert::class, 'alert'], 20, 2);
        do_action('alert_red', $alert, $name);
    }

    public static function alert($alert, $name)
    {
        return require_once ('exception/src/index.html');
    }
}