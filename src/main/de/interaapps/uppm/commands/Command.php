<?php
namespace de\interaapps\uppm\commands;

interface Command {
    public function execute(array $args);
}