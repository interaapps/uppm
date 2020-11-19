<?php
$uppmlock = (object) [];
if (file_exists("uppm.locks.json"))
    $uppmlock = json_decode(file_get_contents("uppm.locks.json"));


spl_autoload_register(function($class) {
    global $uppmlock;
    if (isset($uppmlock->directnamespaces->{$class}))
        @include_once "./".str_replace("\\","/",$uppmlock->directnamespaces->{$class});
    else if(file_exists("./".str_replace("\\","/",$class).".php"))
        @include_once "./".str_replace("\\","/",$class).".php";
    else if(file_exists("./modules/".str_replace("\\","/",$class).".php"))
        @include_once "./modules/".str_replace("\\","/",$class).".php";
    // Special stuff
    else if(file_exists("./src/".str_replace("\\","/",$class).".php"))
        @include_once "./src/".str_replace("\\","/",$class).".php";
    else if(isset($uppmlock->namespace_bindings)) {
        foreach ($uppmlock->namespace_bindings as $namespaceBinding => $folder){
            if (substr($class, 0, strlen($namespaceBinding)) === $namespaceBinding) {
                if (file_exists($folder.'/'.$class.".php")) {
                    @include_once $folder.'/'.$class.".php";
                    break;
                }
            }
        }
    }
});

if (isset($uppmlock->initscripts))
    foreach ($uppmlock->initscripts as $script) {
        @include_once $script;
    }
