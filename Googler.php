<?php

namespace MK11;

require_once __DIR__."/vendor/autoload.php";

use PHPHtmlParser\Dom;


class Googler
{
    protected $search_expression;
    protected $url;

    public function __construct(string $expr)
    {
        $this->search_expression = urlencode($expr);
        $this->domParser = new Dom();

        $this->url = "https://google.com/search?q={$this->search_expression}&client=ubuntu";
    }

    public function find(int $pages_amount) :array
    {
        
        $url = $this->url;

        $all_files = [];

        for ($i=0; $i < $pages_amount ; $i++) { 

            $result = file_get_contents($url);
            $links = $this->pageLinks($result); 

            // $amount_of_links = count($links);
            // echo "$i => $amount_of_links \n";

            array_push($all_files, $links);

            try {
                $url = $this->nextUrl($result); 
            } catch (\Exception $e) {

                if ($e->getCode() === 10) {
                    // echo $e->getMessage();
                    break;
                }
            }

            // $pb->advance(); 
        }

        // $pb->finish();

        $merged = array_merge(...$all_files);
        $string_form = implode("\n", $merged);

        if (count($merged) > 0) 
            $this->create_file_with_results($pages_amount, $string_form);

        return $merged;
        
    }

    public function create_file_with_results(int $pages, string $data):void
    {
        file_put_contents($this->search_expression . "-" . $pages, $data);
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
       
        if (count($data) === 0) {
            throw new \Exception("lastPage", 10);
        }

        $href = $data[count($data)-1]->href;
        $next_url = "https://google.com" . $href;

        return str_replace('&amp;', '&', $next_url);
    }

}


