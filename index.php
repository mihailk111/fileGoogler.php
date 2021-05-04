<?php

require("./fileGoogler.php");


$google = new fileGoogler("passwords", "xlsx");

print_r($google->find(3));


function dd($in)
{
    var_dump($in);
    die;
}