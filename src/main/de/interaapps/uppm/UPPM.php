<?php

namespace de\interaapps\uppm;

use de\interaapps\jsonplus\JSONPlus;
use de\interaapps\uppm\commands\AddAutoloadCommand;
use de\interaapps\uppm\commands\BuildCommand;
use de\interaapps\uppm\commands\CreateCommand;
use de\interaapps\uppm\commands\GHCCommand;
use de\interaapps\uppm\commands\HelpCommand;
use de\interaapps\uppm\commands\InfoCommand;
use de\interaapps\uppm\commands\InitCommand;
use de\interaapps\uppm\commands\InstallCommand;
use de\interaapps\uppm\commands\LockCommand;
use de\interaapps\uppm\commands\ProjectInfoCommand;
use de\interaapps\uppm\commands\ReplCommand;
use de\interaapps\uppm\commands\RunCommand;
use de\interaapps\uppm\commands\ServeCommand;
use de\interaapps\uppm\config\Configuration;
use de\interaapps\uppm\config\LockFile;
use de\interaapps\uppm\helper\Logger;

class UPPM {

    private Logger $logger;
    private array $commands;
    private Project $currentProject;
    private JSONPlus $jsonPlus;
    private string $currentDir;

    public function __construct(private array $args, string|null $dir = null) {
        $this->logger = Logger::createEchoLogger();
        $this->jsonPlus = JSONPlus::createDefault()->setPrettyPrinting(true);

        $this->commands = [
            "install" => new InstallCommand($this),
            "run" => new RunCommand($this),
            "serve" => new ServeCommand($this),
            "autoload" => new AddAutoloadCommand($this),
            "build" => new BuildCommand($this),
            "create" => new CreateCommand($this),
            "project-info" => new ProjectInfoCommand($this),
            "info" => new InfoCommand($this),
            "help" => new HelpCommand($this),
            "init" => new InitCommand($this),
            "lock" => new LockCommand($this),
            "repl" => new ReplCommand($this),

            "ghc" => new GHCCommand($this)
        ];

        if ($dir == null)
            $this->currentDir = getcwd();
        else
            $this->currentDir = $dir;

        $this->commands["i"] = $this->commands["install"];
        $this->commands["r"] = $this->commands["run"];

        $config = new Configuration();
        $lockFile = new LockFile();
        if (file_exists($this->currentDir . "/uppm.json")) {
            $config = Configuration::fromJson(file_get_contents("$this->currentDir/uppm.json"));
        }
        $config->repositories[] = "https://central.uppm.interaapps.de";
        if (file_exists($this->currentDir . "/uppm.locks.json")) {
            $lockFile = LockFile::fromJson(file_get_contents("$this->currentDir/uppm.locks.json"));
        }
        $this->currentProject = new Project(getcwd(), $config, $lockFile);
    }

    public function start(): void {
        if (count($this->args) > 0) {
            foreach ($this->commands as $name => $command) {
                if (strtolower($name) == $this->args[0]) {
                    $shifted = array_shift($this->args);
                    $command->execute($this->args);
                    array_unshift($this->args, $shifted);
                    return;
                }
            }
        }
        $this->logger->err("NOT FOUND");
        $this->commands["help"]->execute([]);
    }

    public static function main($args): void {
        $uppm = new UPPM($args);
        $uppm->start();
    }

    public function getLogger(): Logger {
        return $this->logger;
    }

    public function getCurrentProject(): Project {
        return $this->currentProject;
    }

    public function getCommands(): array {
        return $this->commands;
    }

    public function getJsonPlus(): JSONPlus {
        return $this->jsonPlus;
    }

    public function getCurrentDir(): string {
        return $this->currentDir;
    }

}