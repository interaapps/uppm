<?php

namespace de\interaapps\uppm\commands;


class HelpCommand extends Command {

    public function execute(array $args) {
        $this->uppm->getLogger()->info("Help list:");
        foreach ($this->uppm->getCommands() as $name => $command) {
            $this->uppm->getLogger()->log("- $name");
        }
    }
}