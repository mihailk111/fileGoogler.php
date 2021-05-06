<?php
use PHPHtmlParser\Dom;

require("./fileGoogler.php");

$exp        = "никита ветров казань";
$file_type  = "txt";


$exp = urlencode($exp);


$google = new fileGoogler($exp, $file_type);
echo "\n";
print_r($google->find(10));


function dd($in)
{
    var_dump($in);
    die;
}

