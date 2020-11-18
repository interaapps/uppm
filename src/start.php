#!/usr/bin/env php
<?php
$uppmlock = [];
if (file_exists("uppm.locks.json"))
    $uppmlock = json_decode(file_get_contents("uppm.locks.json"));

$uppmconf = (object)[];
if (file_exists("uppm.json"))
    $uppmconf = json_decode(file_get_contents("uppm.json"));

$serverInfo = @json_decode(@file_get_contents("https://raw.githubusercontent.com/interaapps/uppm-packages/master/uppm.json?".rand(00000, 99999), false, stream_context_create([
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: request"
    ]
])));

define("UPPMINFO", [
    "version"=>"1.2.0",
    "server"=> (isset($serverInfo->list)) ? $serverInfo->list : false
]);

?>