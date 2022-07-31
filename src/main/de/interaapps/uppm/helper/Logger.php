<?php

namespace de\interaapps\uppm\helper;

class Logger {
    public const COLORS = "\033[38;5;95m",
        BLACK = "\033[38;5;0m",
        OKBLUE = "\033[38;5;94m",
        OKGREEN = "\033[38;5;92m",
        WARNING = "\033[38;5;93m",
        FAIL = "\033[38;5;91m",
        ENDC = "\033[0m",
        BOLD = "\033[38;5;1m",
        UNDERLINE = "\033[38;5;4m",
        RED = "\033[38;5;160m",
        GRAY = "\033[38;5;248m",
        DARK_GRAY = "\033[38;5;237m",
        BLUE = "\033[38;5;63m",
        YELLOW = "\033[38;5;33m",
        TURQUIOUS = "\033[38;5;12m",
        PURPLE = "\033[38;5;163m",
        PINK = "\033[38;5;200m",
        ORANGE = "\033[38;5;172m",
        GREEN = "\033[38;5;40m",
        BLINK = "\033[38;5;5m",
        LIGHT_BLUE = "\033[38;5;12m",
        LIGHT_RED = "\033[38;5;196m",
        LIGHT_GREEN = "\033[38;5;46m";

    public const COLOR_MAPPING = [
        "0" => Logger::BLACK,
        "1" => Logger::BLUE,
        "2" => Logger::GREEN,
        "3" => Logger::TURQUIOUS,
        "4" => Logger::RED,
        "5" => Logger::PURPLE,
        "6" => Logger::ORANGE,
        "7" => Logger::GRAY,
        "8" => Logger::DARK_GRAY,
        "9" => Logger::LIGHT_BLUE,
        "a" => Logger::LIGHT_GREEN,
        "b" => Logger::LIGHT_BLUE,
        "c" => Logger::LIGHT_RED,
        "d" => Logger::PINK,
        "e" => Logger::YELLOW,
        "f" => Logger::ENDC,
        "g" => "\033[38;5;210m",
        "h" => "\033[38;5;177m",
    ];

    public function __construct(
        private $echoCallable,
        private $errCallable
    ) {
    }

    public function log(mixed $l, $newLine = true): void {
        if (!is_string($l))
            $l = json_encode($l);

        foreach (Logger::COLOR_MAPPING as $code => $esc) {
            $l = str_replace("§" . $code, $esc, $l);
        }

        ($this->echoCallable)($l . self::ENDC . ($newLine ? "\n" : ""));
    }

    public function err($e): void {
        ($this->errCallable)(self::RED . "ERROR: " . self::ENDC . $e . "\n");
    }

    public function warn($e): void {
        $this->log(self::WARNING . "WARN: " . self::ENDC . $e);
    }

    public function info($e): void {
        $this->log(self::TURQUIOUS . "INFO: " . self::ENDC . $e);
    }

    public function success($e): void {
        $this->log(self::GREEN . "DONE: " . self::ENDC . $e);
    }

    public static function createEchoLogger(): Logger {
        return new Logger(
            function ($s) {
                echo $s;
            },
            function ($s) {
                error_log($s);
            }
        );
    }

    private static int $lastLength = 0;

    public function loadingBar(float $percentage, string $text = ""): void {
        $cols = getenv('COLUMNS') ?? 60;
        if ($cols > 60)
            $cols = 60;

        if (self::$lastLength > strlen($text))
            $text .= str_repeat(" ", self::$lastLength - strlen($text));
        else
            self::$lastLength = strlen($text);

        $width = floor($percentage * $cols);

        $this->log("\r§b[§h" . str_repeat("█", $width) . "§7" . str_repeat("░", $cols - $width) . "§b] §h" . str_pad(floor($percentage * 100) . "%", 4) . " §f" . (trim($text) != "" ? "» " . $text : ''), false);
    }
}