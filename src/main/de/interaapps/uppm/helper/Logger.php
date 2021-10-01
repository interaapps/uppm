<?php
namespace de\interaapps\uppm\helper;

class Logger {
    public const HEADER = "\033[95m",
        OKBLUE = "\033[94m",
        OKGREEN = "\033[92m",
        WARNING = "\033[93m",
        FAIL = "\033[91m",
        ENDC = "\033[0m",
        BOLD = "\033[1m",
        UNDERLINE = "\033[4m",
        RED = "\033[31m",
        BLUE = "\033[34m",
        YELLOW = "\033[33m",
        TURQUIOUS = "\033[36m",
        GREEN = "\033[32m",
        BLINK = "\033[5m",
        BG_RED = "\033[41m",
        BG_BLUE = "\033[44m",
        BG_GREEN = "\033[42m",
        BG_YELLOW = "\033[43m",
        BG_BLACK = "\033[40m";

    public const PREFIX_DONE = "\033[92m Done\033[0m: ",
        PREFIX_WARN = "\033[93m WARNING\033[0m: ",
        PREFIX_INFO = "\033[36m INFO\033[0m: ",
        PREFIX_ERROR = "\033[91m ERROR\033[0m: ";

    private $echoCallable;

    public function __construct($echoCallable){
        $this->echoCallable = $echoCallable;
    }

    public function log($l){
        if (!is_string($l))
            $l = json_encode($l);
        ($this->echoCallable)($l.self::ENDC."\n");
    }

    public function err($e){
        error_log(self::RED."ERROR: ".self::ENDC.$e."\n");
    }

    public function warn($e){
        $this->log(self::WARNING."WARN: ".self::ENDC.$e);
    }

    public function info($e){
        $this->log(self::TURQUIOUS."INFO: ".self::ENDC.$e);
    }

    public static function createEchoLogger() : Logger {
        return new Logger(function($s){
            echo $s;
        });
    }
}