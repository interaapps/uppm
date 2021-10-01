<?php
namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\config\Configuration;
use de\interaapps\uppm\UPPM;

class CreateCommand implements Command {
    private UPPM $uppm;
    public function __construct(UPPM $uppm) {
        $this->uppm = $uppm;
    }

    public function execute(array $args) {
        if (count($args) > 0) {
            $dirName = getcwd()."/".$args[0];
            $this->uppm->getLogger()->info("Creating in $dirName");
            mkdir($dirName);
            $config = new Configuration();
            $config->name = $dirName;
            $config->version = "1.0";
            mkdir("$dirName/src");
            mkdir("$dirName/src/main");
            mkdir("$dirName/src/main/com");
            mkdir("$dirName/src/main/com/example");
            file_put_contents("$dirName/autoload.php", file_get_contents(__DIR__."../../../../../../../autoload.php"));

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

            $config->run = (object) ["start"=>"src/main/bootstrap.php"];

            $config->build = (object) ["run" => "start"];

            file_put_contents($dirName."/uppm.json", $config->json());
        } else {
            $this->uppm->getLogger()->err("Please use 'uppm create {folder_name}'");
        }
    }
}