<?php
/**
 * - ULOLEPHPPACKAGEMANAGER -
 * 
 * Colors
 * Using colors in the CLI!
 * 
 * @author InteraApps
 */
namespace de\interaapps\uppm;

 class Configs {
    public static function getLockFile() {
        if (file_exists(UPPM_CURRENT_DIRECTORY."uppm.locks.json"))
            return json_decode(file_get_contents(UPPM_CURRENT_DIRECTORY."uppm.locks.json"));
        
        file_put_contents(UPPM_CURRENT_DIRECTORY."uppm.locks.json",'
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
        return json_decode(file_get_contents(UPPM_CURRENT_DIRECTORY."uppm.locks.json"));
    }

    public static function getNPPMFile() {
        if (file_exists(UPPM_CURRENT_DIRECTORY."uppm.json"))
            return json_decode(file_get_contents(UPPM_CURRENT_DIRECTORY."uppm.json"));
        
        file_put_contents(UPPM_CURRENT_DIRECTORY."uppm.json",'
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
        return json_decode(file_get_contents(UPPM_CURRENT_DIRECTORY."uppm.json"));
    }
 }
 ?>