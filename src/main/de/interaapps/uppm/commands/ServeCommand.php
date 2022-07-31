<?php
namespace de\interaapps\uppm\commands;


use de\interaapps\uppm\UPPM;

class ServeCommand extends Command {
    public function execute(array $args) {
        $config = $this->uppm->getCurrentProject()->getConfig();
        $port = $config?->serve?->port ?: 8000;
        $routerFile = $config?->serve?->routerFile ?: "";
        $directory = $config?->serve?->directory ?: ".";
        $host = $config?->serve?->host ?: "0.0.0.0";

        while (is_resource(@fsockopen($host, $port))) {
            $port++;
        }

        $this->uppm->getLogger()->info("Starting server on §1http://{$host}:§3{$port}§f!");
        $this->uppm->getLogger()->log("");

        $exec = "cd $directory\nphp -S $host:$port -t ./ ".($routerFile ? escapeshellarg($routerFile) : '');

        system($exec);
        exec($exec);
        shell_exec($exec);
        $this->uppm->getLogger()->info("Error while starting server on $host:$port!");
    }
}