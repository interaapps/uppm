<?php

namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\config\Configuration;

class InfoCommand extends Command {

    public function execute(array $args) {
        $uppmConfig = Configuration::fromJson(file_get_contents(__DIR__ . "/../../../../../../uppm.json"));;

        $this->uppm->getLogger()->log("§3UPPM-Version: §f" . $uppmConfig->version);
        $this->uppm->getLogger()->log("§3PHP-Version:  §f" . phpversion());
    }
}