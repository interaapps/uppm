<?php
namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\helper\Web;
use de\interaapps\uppm\package\Package;
use de\interaapps\uppm\UPPM;

class InstallCommand extends Command {
    public function execute(array $args) {
        if (!file_exists($this->uppm->getCurrentDir()."/modules"))
            mkdir($this->uppm->getCurrentDir()."/modules");
        if (!file_exists($this->uppm->getCurrentDir()."/modules/autoload.php"))
            file_put_contents($this->uppm->getCurrentDir()."/modules/autoload.php", Web::httpRequest("https://central.uppm.interaapps.de/autoload.txtphp"));
        if (count($args) > 0) {
            $split = explode(":", $args[0]);

            $package = Package::getPackage($this->uppm, $split[0], count($split) > 1 ? $split[1] : "latest");

            if ($package->download()) {
                $this->uppm->getLogger()->log("\n\n");
                $this->uppm->getLogger()->info("Adding to uppm.json->modules");
                $package->addToConfig();
                $this->uppm->getLogger()->success("Downloaded $args[0]!");
            }
        } else {
            $this->uppm->getLogger()->info("Installing Modules...");
            $i = 0;
            foreach ($this->uppm->getCurrentProject()->getConfig()->modules as $name=>$version) {
                $package = Package::getPackage($this->uppm, $name, $version);
                $package->download();
                $i++;
            }
            $this->uppm->getLogger()->success("Installed $i packages!");
        }
    }
}