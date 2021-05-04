<?php

require("./fileGoogler.php");

$google = new fileGoogler("passwords", "xlsx");

echo "\n";
print_r($google->find(30));


function dd($in)
{
    var_dump($in);
    die;
}