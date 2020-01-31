<?php
/**
 * - ULOLEPHPPACKAGEMANAGER -
 * 
 * INIT
 * 
 * @author InteraApps
 */

 class Init {

    public static function initFromCLI() {
        $prefix = "\nUPPM INIT: ";
        if (file_exists("uppm.json")) {
            echo $prefix."uppm.json already initialized! Do you want to reinitialize it? (yes, y)";

        }
        echo $prefix."Project name (Only lower case, numbers and underscores): ";
        $name = readline();
        if (!preg_match('#^[a-z0-9_]+$#', $name) && $name != "") {
            return Colors::PREFIX_ERROR."Only lower case, numbers and underscores are allowed!\n";
        }

        echo $prefix."Project version (Numbers and dots): ";
        $version = readline();
        if (!preg_match('#^[0-9.]+$#', $version) && $version != "") {
            return Colors::PREFIX_ERROR."Only numbers and dots!\n";
        }

        echo $prefix."Description [OPTIONAL]: ";
        $description = readline();

        echo $prefix."Author [OPTIONAL]: ";
        $author = readline();

        echo $prefix."Keywords (Split with a komma) [OPTIONAL]: ";
        $keywords = explode(',', readline());

        self::initProject($name, $version, $description, $author, $keywords);
        Colors::info("You can install the autoloader by typing: uppm autoload");
    }

    public static function initProject(
        $name="uppmproject",
        $version="1.0",
        $description="",
        $author= "",
        $keywords=[]
    ) {
        $file = Configs::getNPPMFile();
        $file->name=$name;
        $file->version=$version;
        $file->description=$description;
        $file->author   =$author;
        $file->keywords =$keywords;
        file_put_contents("uppm.json", json_encode($file, JSON_PRETTY_PRINT));
    }

 }
 ?>