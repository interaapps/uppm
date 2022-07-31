<?php

namespace de\interaapps\uppm\config;

use de\interaapps\jsonplus\attributes\Serialize;
use de\interaapps\jsonplus\JSONModel;
use de\interaapps\uppm\UPPM;

class LockFile {
    use JSONModel;

    public object|null $namespaceBindings;
    public object|null $directNamespaceBindings;
    public object|null $modules;
    public array $initScripts;

    public function __construct() {
        $this->namespaceBindings = (object)[];
        $this->directNamespaceBindings = (object)[];
        $this->modules = (object)[];
        $this->initScripts = [];
    }

    public function save(UPPM $uppm): void {
        $lockNameSpaces = [];
        $namespaceBindingsKeys = array_keys((array)$this->namespaceBindings);
        usort($namespaceBindingsKeys, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        foreach ($namespaceBindingsKeys as $key) {
            $this->addRec($uppm, $this->namespaceBindings->{$key}, $key, $lockNameSpaces);
        }

        if (!is_dir($uppm->getCurrentDir() . "/modules"))
            mkdir($uppm->getCurrentDir() . "/modules");

        file_put_contents($uppm->getCurrentDir() . "/modules/autoload_namespaces.php", "<?php
// UPPM generates this file to add a more efficient way of autoloading a class.
// Do not change something in here.

return " . var_export($lockNameSpaces, true) . ";");

        $this->initScripts = array_values($this->initScripts);
        file_put_contents($uppm->getCurrentDir() . "/uppm.locks.json", $this->toJson());
    }

    private function addRec(UPPM $uppm, $dir, $key, &$lockNameSpaces): void {
        if (is_dir($dir)) {
            $d = @scandir($dir);
            if ($d === false)
                return;
            foreach ($d as $f) {
                if ($f != ".." && $f != ".") {
                    if (is_dir($dir . "/" . $f)) {
                        $this->addRec($uppm, $dir . "/" . $f, $key, $lockNameSpaces);
                    } else {
                        $class = str_replace(".php", "", $f);
                        $lockNameSpaces[$key . "\\" . $class] = $dir . "/" . $f;
                    }
                }
            }
        }
    }
}