<?php

namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\UPPM;

abstract class Command {
    public function __construct(
        protected UPPM $uppm
    ) {
    }

    public abstract function execute(array $args);
}