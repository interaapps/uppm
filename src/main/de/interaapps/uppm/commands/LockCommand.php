<?php
namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\UPPM;

class LockCommand implements Command {
    public function __construct(private UPPM $uppm) {
    }

    public function execute(array $args) {
        $this->uppm->getCurrentProject()->getConfig()->lock($this->uppm->getCurrentProject()->getLockFile(), ".");
    }
}