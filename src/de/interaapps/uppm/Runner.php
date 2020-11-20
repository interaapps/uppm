<?php
namespace de\interaapps\uppm;

use de\interaapps\uppm\cli\Colors;

class Runner {

    private $file = null;
    private $args = [];
    private $runCommands = [];
    private $compileFirst = false;
    private $uppmLogs = true;
    private $exec = [];


    public function run(){
        global $CLI;
        if ($this->file == null) {
            if (!$this->compileFirst)
                Colors::error("Compile File not set!");
            else {
                if ($this->uppmLogs)
                    Colors::info("Compiling...");
                $build = $CLI->getCommands()["build"]();
                $this->file = "./".$build->getOutputLocation()."/".$build->getOutputFile();
            }
        }

        foreach ($this->exec as $exec)
            system($exec);


        $command = "php ";
        $command .= $this->file;
        foreach ($this->args as $arg)
            $command .= " ".$arg;

        if ($this->uppmLogs)
            Colors::info("Running PHP-Project with command: ".$command);
        system($command);
    }

    public function setFile(string $file): Runner {
        $this->file = $file;
        return $this;
    }

    public function setArgs(array $args): Runner {
        $this->args = $args;
        return $this;
    }

    public function setRunCommands(array $runCommands): Runner {
        $this->runCommands = $runCommands;
        return $this;
    }

    public function setCompileFirst(bool $compileFirst): Runner {
        $this->compileFirst = $compileFirst;
        return $this;
    }

    public function getFile(): string {
        return $this->file;
    }

    public function getArgs(): array {
        return $this->args;
    }

    public function getRunCommands(): array {
        return $this->runCommands;
    }

    public function getCompileFirst(): bool {
        return $this->compileFirst;
    }

    public function isUppmLogs(): bool {
        return $this->uppmLogs;
    }

    public function setUppmLogs(bool $uppmLogs): void {
        $this->uppmLogs = $uppmLogs;
    }

    public function getExec(): array {
        return $this->exec;
    }

    public function setExec(array $exec): void {
        $this->exec = $exec;
    }


}

?>
