<?php
namespace de\interaapps\uppm\commands;


use de\interaapps\uppm\package\Package;
use de\interaapps\uppm\package\UPPMPackage;
use de\interaapps\uppm\UPPM;

class AddAutoloadCommand implements Command {
    public function __construct(
        private UPPM $uppm
    ) {

    }

    public function execute(array $args) {
        $this->uppm->getLogger()->info("Downloading autoload.php...");
        file_put_contents($this->uppm->getCurrentProject()->getDir()."autoload.php", file_get_contents("https://raw.githubusercontent.com/interaapps/uppm/master/autoload.php"));
    }

}