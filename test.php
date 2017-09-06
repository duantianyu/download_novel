<?php
/**
 * test download novel from http://www.xs74.com
 * User: tianyu
 * Date: 2017/9/5
 * Time: 10:09
 */

set_time_limit(0);
ini_set('memory_limit', -1);
date_default_timezone_set('PRC');


require 'vendor/autoload.php';
require 'DownloadNovel.php';


use Download\DownloadNovel;

/*
$download = new DownloadNovel('http://www.xs74.com/novel/wodiweixinliansanjie/txt.html');
$download->start = 2;
$download->end = 5;
$download->chapter_labels = '#Chapters li a';
$download->content_labels = '#content';
$download->page_labels = '#content .text';
$download->getTxt();
echo "done\n";
*/



//$download = new DownloadNovel('http://www.xs74.com/novel/zuiqiangchaoshenkuangbaoxitong/');
//$download = new DownloadNovel('http://www.xs74.com/novel/wudaozhizun/');
$download = new DownloadNovel('http://www.xs74.com/novel/wushenkongjian/');

$download->start = 2;
$download->end = 5;
$download->title_labels = '.bookname h1';
$download->chapter_labels = '#list dd a';
$download->content_labels = '#content';
$download->page_labels = '#content .text';
$download->getTxt();
echo "done\n";



