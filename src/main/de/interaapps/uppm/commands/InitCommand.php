<?php
namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\UPPM;

class InitCommand implements Command {
    private UPPM $uppm;
    public function __construct(UPPM $uppm) {
        $this->uppm = $uppm;
    }

    public function execute(array $args) {
    }
}