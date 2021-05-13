<?php

require_once("./fileGoogler.php");

use MK11\fileGoogler;

$exp        = "\"hello world\"";
$file_type  = "pdf";



$google = new fileGoogler($exp, $file_type);

echo "\n";
print_r($google->find(3));


function dd($in)
{
    var_dump($in);
    die;
}

