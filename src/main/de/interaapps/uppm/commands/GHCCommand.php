<?php

namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\helper\Web;

class GHCCommand extends Command {
    public function execute(array $args) {
        $a = count($args) == 1 ? $args[0] : $this->uppm->getCurrentProject()->getConfig()->name;
        if (!str_contains($a, "/"))
            $a = "_/$a";
        Web::httpRequest("https://central.uppm.interaapps.de/$a/checkgithub");
        $this->uppm->getLogger()->info("Done!");
    }
}