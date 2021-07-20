<?php
namespace Form;
use Dbdental\Snsearch;

class Search_parts extends \Db
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', function(){
            wp_register_style('style_css', plugins_url('db/Form/snsearch/style.css'));
            wp_enqueue_style('style_css');
            wp_register_script('min_js', plugins_url('db/Form/js/jquery.min.js'));
            add_action('wp_footer', function(){
                wp_enqueue_style('min_js');
            });

        });

        add_shortcode('snsearch', [&$this, 'require_sn']);
    }

    public function require_sn()
    {
        ob_start();
        require_once ('snsearch/index.html');
        return ob_get_clean();
    }

    private function get_sn()
    {
        if(isset($_GET['sn'])):
            $sn = new Snsearch($this->db());
            $result = $sn->search($_GET['sn']);
            return $result;
        endif;
    }
}