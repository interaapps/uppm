<?php
namespace de\interaapps\uppm\commands;


use de\interaapps\uppm\UPPM;

class RunCommand extends Command {
    public function execute(array $args) {
        if ($this->uppm->getCurrentProject()->getConfig()->phpVersion > phpversion())
            $this->uppm->getLogger()->warn("The app requires php version §h".$this->uppm->getCurrentProject()->getConfig()->phpVersion."§f. You are using version §c".phpversion()."§f.");

        if (count($args) > 0 && isset($this->uppm->getCurrentProject()->getConfig()->run->{$args[0]})) {
            $cmd = $args[0];
            $shifted = array_shift($args);
            system("php " . $this->uppm->getCurrentProject()->getConfig()->run->{$cmd} . " " . implode(" ", $args));
            array_unshift($args, $shifted);
        } else {
            $this->uppm->getLogger()->info("Available run-list");
            foreach ($this->uppm->getCurrentProject()->getConfig()->run as $key=>$val) {
                $this->uppm->getLogger()->log("- §2{$key}§f: §7`{$val}`");
            }
        }
    }
}