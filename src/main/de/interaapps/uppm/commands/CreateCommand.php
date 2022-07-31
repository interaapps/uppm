<?php

namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\config\Configuration;
use de\interaapps\uppm\helper\Files;
use de\interaapps\uppm\package\Package;
use de\interaapps\uppm\UPPM;

class CreateCommand extends Command {
    public function execute(array $args) {
        if (count($args) > 0) {
            $name = $args[0];
            $dirName = $this->uppm->getCurrentDir() . "/" . $name;
            if (file_exists($dirName)) {
                $this->uppm->getLogger()->info("The folder already exists");
                if (strtolower(readline("Do you want to continue? (y/N): ")) != "y")
                    exit();
            }
            $this->uppm->getLogger()->info("Creating in $dirName");


            if (count($args) > 1) {
                $src = $args[1];
                $splitSrc = explode(":", $src);
                $package = Package::getPackage(new UPPM([], getcwd() . "/" . $dirName), $splitSrc[0], count($splitSrc) > 1 ? $splitSrc[1] : "latest");
                [$zipOutDir, $tmpName] = $package->unpack();

                Files::copyDir($zipOutDir, $dirName);
                Files::deleteDir($tmpName);

                $tmpUPPM = new UPPM(["install"], $dirName);
                $tmpUPPM->start();
            } else {
                $config = new Configuration();
                $config->name = $name;
                $config->version = "1.0";

                mkdir($dirName);
                mkdir("$dirName/src");
                mkdir("$dirName/src/main");
                mkdir("$dirName/src/main/com");
                mkdir("$dirName/src/main/com/example");
                file_put_contents("$dirName/autoload.php", file_get_contents(__DIR__ . "../../../../../../../autoload.php"));

                file_put_contents("$dirName/src/main/bootstrap.php", '#!/usr/bin/env php
<?php
error_reporting(E_ERROR | E_PARSE | E_NOTICE);
chdir(".");
(include "autoload.php")(mod: "main");
array_shift($argv);
com\example\Example::main($argv);');

                file_put_contents("$dirName/src/main/com/example/Example.php", '<?php
namespace com\\example;

class Example {
    public static function main(array $args) : void {
        echo "Hello World!";
    }
}
');

                $config->run = (object)["start" => "src/main/bootstrap.php"];

                $config->build = (object)["run" => "start"];

                file_put_contents($dirName . "/uppm.json", $config->toJson());
            }
        } else {
            $this->uppm->getLogger()->err("Please use 'uppm create {folder_name}'");
        }
    }
}