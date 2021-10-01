<?php
namespace de\interaapps\uppm\commands;


use de\interaapps\uppm\UPPM;

class ServeCommand implements Command {
    public function __construct(private UPPM $uppm) {
    }

    public function execute(array $args) {
        $config = $this->uppm->getCurrentProject()->getConfig();
        $port = $config?->serve?->port ?: 8000;
        $routerFile = $config?->serve?->routerFile ?: "";
        $directory = $config?->serve?->directory ?: ".";
        $host = $config?->serve?->host ?: "0.0.0.0";

        $this->uppm->getLogger()->info("Starting server on http://$host:$port!");

        $exec = "cd $directory\nphp -S $host:$port -t ./ $routerFile";

        system($exec);
        exec($exec);
        shell_exec($exec);
        $this->uppm->getLogger()->info("Error while starting server on $host:$port!");
    }
}