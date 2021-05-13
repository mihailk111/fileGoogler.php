<?php

namespace MK11;

require_once __DIR__."/vendor/autoload.php";
require_once __DIR__ . "/Googler.php";

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\StreamOutput;
use PHPHtmlParser\Dom;
use MK11\Googler;


class fileGoogler extends Googler
{
    private $fileType;

    public function __construct($expr = "settings", $ftype = "txt")
    {
        parent::__construct($expr);
        $this->fileType = $ftype;
        $this->url = "https://google.com/search?q={$this->search_expression}+filetype:{$this->fileType}&client=ubuntu";
    }

    public function create_file_with_results(int $pages, string $data):void
    {
        file_put_contents($this->search_expression . "-" .$this->fileType."-". $pages, $data);
    }

}