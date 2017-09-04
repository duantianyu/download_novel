<?php
set_time_limit(0);
ini_set('memory_limit', -1);
date_default_timezone_set('PRC');

/**
 * download novel from http://www.xs74.com
 * User: tianyu
 *
 */



require 'vendor/autoload.php';

use DiDom\Document;

$download = new download();
$download->getTxt();
echo "done\n";

class download
{
    public $url = 'http://www.xs74.com/novel/wodiweixinliansanjie/txt.html'; //首页链接
    public $href; //子页面链接
    public $file_name; //文件名
    public $arr_href = []; //已经抓取过的页面
    public $html;
    public $parse_html;
    public $content;
    public $page;
    public $start = 0;
    public $end = 0;


    public function __construct(){
        $this->file_name = date('mdHis') . '.txt';
    }


    public function is_in(){
        if(in_array($this->href, $this->arr_href)){
            return true;
        }else{
            $this->arr_href[] = $this->href;
        }
    }


    public function to_utf8(){
        $encode = mb_detect_encoding($this->html, array("ASCII", "GB2312", "GBK", "UTF-8", "BIG5"));
        if ($encode != 'utf8') {
            $this->html = mb_convert_encoding($this->html, 'utf8', $encode);
        }
    }


    public function getTxt(){
        $this->html = file_get_contents($this->url);

        $this->to_utf8();
        $html_length = strlen($this->html);
        if($html_length < 500){
            echo 'Sorry! 无法抓取该网页';
        }
        //echo $html_res;
        $this->parse_html = new Document($this->html);
        $lia = $this->parse_html->find('#Chapters li a');
        foreach ($lia as $k => $v){
            //开始章节
            if($this->start > 0 && $k < $this->start){
                continue;
            }
            //结束章节
            if($this->end > 0 && $k > $this->end){
                break;
            }
            //echo $v->text(), $k;
            $this->href = $v->attr('href');
            if($this->is_in()){
                continue;
            }


            $this->parse_html();

            $arr_sub_href = $this->page->find('a[href]');
            $sub_count = count($arr_sub_href);
            foreach ($arr_sub_href as $key => $val){
                if($key == ($sub_count - 1)) break;
                $this->href = $val->attr('href');
                if($this->is_in()){
                    continue;
                }

                $this->parse_html();
            }

            unset($this->content);
            unset($this->page);
            unset($arr_sub_href);
            unset($sub_count);
            unset($my_sub_con);

            echo $k, "ok\n";
        }

    }


    public function parse_html(){
        $this->html = file_get_contents($this->href);
        $this->to_utf8();
        $this->parse_html = new Document($this->html);
        $this->content = $this->parse_html->find('#content')[0]->text();
        $this->page = $this->parse_html->find('#content .text')[0];
        $this->content = str_replace($this->page->text(), '', $this->content);
        $this->content = str_replace('　　', "\n", $this->content);
        $this->write();
    }


    public function write(){
        file_put_contents($this->file_name, $this->content, FILE_APPEND);
        $this->content = '';
    }


    public function __destruct()
    {
        unset($this->arr_href);
        unset($this->html);
        unset($this->parse_html);
        unset($this->href);
    }
}