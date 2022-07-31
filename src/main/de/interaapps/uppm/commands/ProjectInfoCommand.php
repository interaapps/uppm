<?php
namespace de\interaapps\uppm\commands;

class ProjectInfoCommand extends Command {
    public function execute(array $args) {
        var_dump($this->uppm->getCurrentProject()->getConfig());
        var_dump($this->uppm->getCurrentProject()->getLockFile());
    }
}