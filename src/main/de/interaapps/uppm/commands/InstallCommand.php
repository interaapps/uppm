<?php
namespace de\interaapps\uppm\commands;


use de\interaapps\uppm\helper\Web;
use de\interaapps\uppm\package\GithubPackage;
use de\interaapps\uppm\package\Package;
use de\interaapps\uppm\package\UPPMPackage;
use de\interaapps\uppm\UPPM;

class InstallCommand implements Command {
    public function __construct(
        private UPPM $uppm
    ) {

    }

    public function execute(array $args) {
        if (!file_exists(getcwd()."/modules"))
            mkdir(getcwd()."/modules");
        if (!file_exists(getcwd()."/modules/autoload.php"))
            file_put_contents(getcwd()."/modules/autoload.php", Web::httpRequest("https://central.uppm.interaapps.de/autoload.txtphp"));
        if (count($args) > 0) {
            $split = explode(":", $args[0]);

            $package = Package::getPackage($this->uppm, $split[0], count($split) > 1 ? $split[1] : "latest");

            if ($package->download()) {
                $this->uppm->getLogger()->info("Adding to uppm.json->modules");
                $package->addToConfig();
            }
        } else {
            $this->uppm->getLogger()->info("Installing Modules...");
            foreach ($this->uppm->getCurrentProject()->getConfig()->modules as $name=>$version) {
                $package = Package::getPackage($this->uppm, $name, $version);
                $package->download();
            }
        }
    }
}