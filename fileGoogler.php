<?php
require __DIR__."/vendor/autoload.php";

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\StreamOutput;

use PHPHtmlParser\Dom;



class fileGoogler
{
    private $search_expression = "password";
    private $fileType = "txt";

    public function __construct($expr, $ftype)
    {
        $this->fileType = $ftype;
        $this->search_expression = $expr;
        $this->domParser = new Dom();
    }

    public function find($pages_amount)
    {
        $pb = new ProgressBar(new StreamOutput(STDOUT), $pages_amount);        

        $pb->start();

        $url = "https://google.com/search?q={$this->search_expression}+filetype:{$this->fileType}&client=ubuntu";

        $all_files = [];

        for ($i=0; $i < $pages_amount ; $i++) { 

            $result = file_get_contents($url);
            $links = $this->pageLinks($result); 

            array_push($all_files, $links);

            $url = $this->nextUrl($result); 

            // file_put_contents("test.html", file_get_contents($url));
            // die;
            // try {
            //     $url = $this->nextUrl($result); 
            // } catch (Exception $e) {
            //     if ($e->getCode() === 10) break;
            // }

            $pb->advance(); 
        }

        $pb->finish();
        return array_merge(... $all_files) ;
        // return $all_files;
        
    }

    private function pageLinks(string $html) : array
    {

        $dom = $this->domParser; 
        $dom->loadStr($html);
        $mainLinks = $dom->find("#main div div div a");

        $array = [];
        foreach ($mainLinks as $item) { $array []= $item->href; } 
        $mainLinks = $array;

        $mainLinks = array_filter($mainLinks, function($elem) {
            if (str_contains($elem, '/url')) return $elem; 
        });
        $mainLinks = array_unique($mainLinks);

        $mainLinks = array_map(function($elem){
            preg_match('/\/url\?q=(.+?)\&amp;/', $elem, $match); // &amp; shows the end of the url
            return $match[1] ?? "NOTHING";
        }, $mainLinks);    

        return $mainLinks;
    }

    private function nextUrl(string $html) : string
    {
        $dom = $this->domParser;
        $dom->loadStr($html);
        $data = $dom->find('footer div div div a');

        // if (!isset($data[count($data)-1]->href)) {
        //     throw new Exception("lastPage", 10);
        // }

        $next_url = "https://google.com" . $data[count($data)-1]->href;
        // dd($next_url);
        return str_replace('&amp;', '&', $next_url);
    }
}
