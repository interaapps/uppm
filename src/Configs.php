<?php
/**
 * - ULOLEPHPPACKAGEMANAGER -
 * 
 * Colors
 * Using colors in the CLI!
 * 
 * @author InteraApps
 */

 class Configs {
    public static function getLockFile() {
        if (file_exists("uppm.locks.json"))
            return json_decode(file_get_contents("uppm.locks.json"));
        
        file_put_contents("uppm.locks.json",'
        {
            "init_scripts": [
        
            ],
            "cli_scripts": {
        
            },
            "packages": {
                
            },
            "directnamespaces": {
                
            }
        }
        ');
        return json_decode(file_get_contents("uppm.locks.json"));
    }

    public static function getNPPMFile() {
        if (file_exists("uppm.json"))
            return json_decode(file_get_contents("uppm.json"));
        
        file_put_contents("uppm.json",'
        {
            "name": "abc",
            "version": "1.0",
            "description": "",
            "author": "",
            "keywords": [],
            "modules": { },
            "namespace_bindings": { },
            "namespaces": { }
        }
        ');
        return json_decode(file_get_contents("uppm.json"));
    }
 }
 ?>