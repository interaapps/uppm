#!/usr/bin/env php
<?php
$uppmlock = [];
if (file_exists("uppm.locks.json"))
    $uppmlock = json_decode(file_get_contents("uppm.locks.json"));

$uppmconf = [];
if (file_exists("uppm.json"))
    $uppmconf = json_decode(file_get_contents("uppm.json"));

define("UPPMINFO", [
    "version"=>"1.0"
]);

?>