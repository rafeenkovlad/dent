<?php
namespace Form\chat;
use App\Models\Telegram_group;

class Telegramgroup
{
    private static $messages;
    public static function getTemplate()
    {
        add_action('wp_enqueue_scripts', function(){
            //Открытие картинки в полный размер на странице с товарным листом
            wp_register_style('chat_css', plugins_url('db/Form/telegram/style.css'));
            wp_register_style('bootstrap4_css', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css');
            wp_enqueue_style('chat_css');
            wp_enqueue_style('bootstrap4_css');
            wp_register_script('telegram_js', plugins_url('db/Form/telegram/script.js'));
            wp_enqueue_script('telegram_js');
            wp_register_script('min_js', plugins_url('db/Form/js/jquery.min.js'));
        });
        add_action('wp_footer', function() {
            wp_enqueue_script('min_js');
        });

        add_shortcode('telegramgroup', [Telegramgroup::class, 'getPage']);
    }

    public static function getPage()
    {
        self::preloadMessage();
        require_once ('telegram/index.html');
    }

    private static function preloadMessage($message = Telegram_group::class)
    {
        $json = $message::GetMessage()
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();
        $arr =& $json;
        $mimes = array(
            // Image formats
            'jpg|jpeg|jpe'                 => 'image/jpeg',
            'gif'                          => 'image/gif',
            'png'                          => 'image/png',
            'bmp'                          => 'image/bmp',
            'tif|tiff'                     => 'image/tiff',
            'ico'                          => 'image/x-icon'
        );

        foreach ($arr as $text)
        {

            $text = json_decode($text->message, true);
            $str = explode("\n",$text['bot']??$text['message']['text']);
            $file = $text['message']['file'];
            $filetype = wp_check_filetype(plugins_url('db/bot/file/'.$file), $mimes);

            if(!isset($text['bot'])):
                self::$messages[] = [
                    'from' => $text['message']['from']['first_name'],
                    'date' => $date = date('H:i d.m.y', $text['message']['date']),
                    'text' => implode("<br />", $str),
                    'file' => ($filetype['ext'])?'<img class="list-gods-img" data-image='.plugins_url('db/bot/file/'.$file).' src='.plugins_url('db/bot/file/'.$file).'':Null
                    ];
            else:
                self::$messages[] = [
                    'from' => 'dentaline_bot',
                    'date' => $date,
                    'text' => trim(implode("<br />", $str), "<br />"),
                    'float' => 'right'
                    ];
            endif;
        }
    }
}