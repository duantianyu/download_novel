<?php
/**
 * download novel
 * User: tianyu
 *
 */
namespace Download;


use DiDom\Document;



class DownloadNovel
{
    public $url = ''; //章节列表链接
    public $href; //子页面链接
    public $file_name; //文件名
    public $title; //标题
    public $arr_href = []; //已经抓取过的页面
    public $html; //页面html
    public $parse_html; //解析过的html
    public $title_labels; //章节标签
    public $chapter_labels; //章节列表标签
    public $content_labels; //章节内容标签
    public $page_labels; //子章节分页标签
    public $content; //内容
    public $page; //分页
    public $start = 0; //开始章节
    public $end = 0; //结束章节


    public function __construct($url = ''){
        $this->file_name = date('mdHis') . '.txt';
        if($url){
            $this->url = $url;
        }
        if(!$this->url){
            exit('请输入章节列表url');
        }
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
        $lia = $this->parse_html->find($this->chapter_labels);
        foreach ($lia as $k => $v){
            $k ++;

            if($this->start > 0 && $k < $this->start){
                continue;
            }

            if($this->end > 0 && $k > $this->end){
                break;
            }

            $this->href = $v->attr('href');
            if($this->is_in()){
                continue;
            }

            $this->parse_html($is_title = 1);
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


    public function parse_html($is_title = 0){
        $this->html = file_get_contents($this->href);
        $this->to_utf8();
        $this->parse_html = new Document($this->html);
        if($is_title && $this->title_labels){
            $this->content = $this->parse_html->find($this->title_labels)[0]->text();
            $this->write();
        }
        $this->content = $this->parse_html->find($this->content_labels)[0]->text();
        $this->page = $this->parse_html->find($this->page_labels)[0];
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