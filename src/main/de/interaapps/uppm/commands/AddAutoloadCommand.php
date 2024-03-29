<?php

namespace de\interaapps\uppm\commands;


class AddAutoloadCommand extends Command {
    public function execute(array $args) {
        $this->uppm->getLogger()->info("Downloading autoload.php...");
        file_put_contents($this->uppm->getCurrentProject()->getDir() . "/autoload.php", file_get_contents("https://raw.githubusercontent.com/interaapps/uppm/master/autoload.php"));
    }

}