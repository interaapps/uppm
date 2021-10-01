<?php
namespace de\interaapps\uppm\commands;


use de\interaapps\uppm\UPPM;

class RunCommand implements Command {
    public function __construct(private UPPM $uppm) {
    }

    public function execute(array $args) {
        if (count($args) > 0 && isset($this->uppm->getCurrentProject()->getConfig()->run->{$args[0]})) {
            $cmd = $args[0];
            $shifted = array_shift($args);
            system("php " . $this->uppm->getCurrentProject()->getConfig()->run->{$cmd} . " " . implode(" ", $args));
            array_unshift($args, $shifted);
        } else {
            foreach ($this->uppm->getCurrentProject()->getConfig()->run as $key=>$val) {
                $this->uppm->getLogger()->info("Available run-list");
                $this->uppm->getLogger()->log("- $key: `$val`");
            }
        }
    }
}