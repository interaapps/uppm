<?php
/**
 * - ULOLEPHPPACKAGEMANAGER -
 * 
 * CLI
 * 
 * @author InteraApps
 */
namespace de\interaapps\uppm\cli;

 class CLI {
    public $commands = [];
    public $descriptions = [];
    /**
     * Change the not found errormessage
     */
    public $errorMessage;
    /** 
     * Shows a list with all commands on function not found error
     */
    public $showArgsOnError=true;

    
    /**
     * Register a new command
     * @param String function-name (Command)
     * @param Function function (example:function() {return "Hello world";})
     * @param String (Optional) Description
     */
    public function register(string $name, $func, string $description="") {
        $this->commands[$name] = $func;
        $this->descriptions[$name] = $description;
    }

    /**
     * Runs a command
     */
    public function run($run) {
        if (isset($this->commands[$run])) {
            $function = ($this->commands[$run]);
            echo $function($run);
        } else {
            if ($this->errorMessage != null) 
                echo $this->errorMessage;
            else
                echo Colors::PREFIX_ERROR."Function \"".$run."\" not found!\n";
            
            
            if ($this->showArgsOnError) {
                $showArgs = Colors::PREFIX_DONE."Those are some valid functions: ";
                foreach ($this->commands as $command=>$value) {
                    $showArgs .= "\n  \033[92m- \033[0m".$command.": ".$this->descriptions[$command];
                }
                echo $showArgs."\n";
            }

        }
    }

     public function getCommands(): array {
         return $this->commands;
     }

     public function getDescriptions(): array {
         return $this->descriptions;
     }
 }

 ?>