<?php
namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\helper\Web;
use de\interaapps\uppm\UPPM;

class GHCCommand implements Command {
    public function __construct(private UPPM $uppm) {
    }

    public function execute(array $args) {
        if (count($args) == 1) {
            if (!str_contains($args[0], "/"))
                $args[0] = "_/$args[0]";
            Web::httpRequest("https://central.uppm.interaapps.de/$args[0]/checkgithub");
            $this->uppm->getLogger()->info("Done!");
        }
    }
}