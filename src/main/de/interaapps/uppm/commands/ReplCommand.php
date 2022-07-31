<?php

namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\helper\Logger;
use Exception;

class ReplCommand extends Command {
    public function execute(array $args) {
        self::$uppm->getLogger()->log(Logger::BLUE . "UPPM-Repl on PHP-Version " . phpversion());
        $this->replLoop();
    }

    public static function replLoop() {
        $opened = false;
        $command = "";

        while (true) {
            register_shutdown_function(self::class . "::handleFatal");
            set_error_handler(self::class . "::handleError");

            self::readlineCompletionHandler();

            if ($opened !== false) {
                $command .= "\n" . readline("... ");
            } else {
                $command = readline(">>> ");
            }

            $lastChar = substr(trim($command), -1);
            if ($lastChar == '\\' || $lastChar == '(' || $lastChar == '{' || $lastChar == '[' || $lastChar == ',') {
                $opened = true;
                $command = rtrim($command, "\\");
            } else
                $opened = false;

            echo Logger::ENDC;
            if ($command == "exit")
                die();
            if (!$opened) {
                try {
                    readline_add_history($command);
                    if (!str_contains($command, ";") && !str_contains($command, "return") && !str_contains($command, "echo"))
                        $command = "return " . $command;

                    $return = eval("" . $command . ";");
                    $returnJSON = json_encode($return, JSON_PRETTY_PRINT);
                    echo "\n" . self::beautifulOutput($return) . "\n";

                    self::readlineCompletionHandler();
                } catch (Exception $e) {
                    self::$uppm->getLogger()->err($e->getMessage());
                }
            }
        }
    }

    public static function beautifulOutput($in, $indent = "") {
        $out = "";

        if (!isset($in)) {
            $out = Logger::GRAY . "null" . Logger::ENDC;
        } else if (is_array($in) || is_object($in)) {
            $isObject = is_object($in);
            if ($isObject) {
                $out .= Logger::BLUE . get_class($in) . Logger::ENDC . " ";
            }
            $out .= Logger::BLUE . ($isObject ? "{" : "[") . Logger::ENDC . "\n";
            foreach ($in as $key => $value) {
                $out .=
                    $indent . "   " . self::beautifulOutput($key, $indent . "   ") .
                    Logger::YELLOW . ": " . Logger::ENDC .
                    self::beautifulOutput($value, $indent . "   ") .
                    "\n";
            }
            $out .= $indent . Logger::BLUE . ($isObject ? "}" : "]") . Logger::ENDC;
        } else if (is_numeric($in)) {
            $out .= Logger::TURQUIOUS . $in . Logger::ENDC;
        } else if (is_string($in)) {
            $rand = rand(11111, 99999);
            // This happens if you don't want to parse it your own xD
            $out .= Logger::GREEN . str_replace("n--cn10" . $rand . "3e9--n", "\n", json_encode(str_replace("\n", "n--cn10" . $rand . "3e9--n", $in))) . Logger::ENDC;
        } else if (is_bool($in)) {
            $out .= ($in ? Logger::GREEN : Logger::RED) . json_encode($in) . Logger::ENDC;
        } else {
            $out .= json_encode($in);
        }

        return $out;
    }


    public static function handleFatal() {
        //set_error_handler("error");
        //register_shutdown_function("fatal");
        $error = error_get_last();

        self::$uppm->getLogger()->err($error["message"]);
        self::replLoop();
    }

    public static function handleError($errno, $errstr, $errfile, $errline) {
        self::$uppm->getLogger()->err($errstr);
    }

    public static function readlineCompletionHandler() {
        readline_completion_function(function ($test, $full) {
            $matches = ["exit", "function", "class", "public", "private", "protected", "var", "echo",
// Helper
                "_decfn;
function NAME () {
echo 'Hello world';
",
                "_deccl;
class NAME {
private \$var;
",
                "_newlines;\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n"
            ];

            // foreach (scandir(".") as $entry){
            //     if ($entry !== "." && $entry !== "..") {
            //         array_push($matches, $entry);
            //     }
            // }

            //$matches = array_merge($matches, get_declared_classes());

            foreach (get_declared_classes() as $clazz) {
                $matches[] = /*str_replace('\\','\\', */
                    $clazz/*)*/
                ;
            }

            foreach (get_defined_vars() as $key => $val) {
                array_push($matches, $key);
            }

            foreach (get_defined_constants() as $key => $val) {
                array_push($matches, $key);
            }

            $declaredFunctions = get_defined_functions();

            if (isset($declaredFunctions["user"]))
                $matches = array_merge($matches, $declaredFunctions["user"]);

            if (isset($declaredFunctions["internal"]))
                $matches = array_merge($matches, $declaredFunctions["internal"]);

            if (($key = array_search('_decfn', $matches)) !== false) {
                unset($matches[$key]);
            }
            if (($key = array_search('_deccl', $matches)) !== false) {
                unset($matches[$key]);
            }

            return $matches;
        });
    }
}