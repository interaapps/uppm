<?php
namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\UPPM;

class InfoCommand implements Command {
    public function __construct(private UPPM $uppm) {
    }

    public function execute(array $args) {
        var_dump($this->uppm->getCurrentProject()->getConfig());
        var_dump($this->uppm->getCurrentProject()->getLockFile());
    }
}