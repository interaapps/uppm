<?php
namespace de\interaapps\uppm\config;

use de\interaapps\uppm\helper\JSONModel;
use de\interaapps\uppm\UPPM;

class LockFile {
    use JSONModel;

    public object|null $namespaceBindings;
    public object|null $directNamespaceBindings;
    public object|null $modules;
    public array|null $initScripts;

    public function __construct() {
        $this->namespaceBindings = (object)[];
        $this->directNamespaceBindings = (object)[];
        $this->modules = (object)[];
        $this->initScripts = [];
    }

    public function save(UPPM $uppm){
        $lockNameSpaces = [];
        $namespaceBindingsKeys = array_keys((array) $this->namespaceBindings);
        usort($namespaceBindingsKeys, function ($a, $b) {
            return strlen($b) - strlen($a);
        });
        foreach ($namespaceBindingsKeys as $key) {
            $this->addRec($this->namespaceBindings->{$key}, $key, $lockNameSpaces);
        }

        file_put_contents($uppm->getCurrentDir()."/modules/autoload_namespaces.php", "<?php
// UPPM generates this file to add a more efficient way of autoloading a class.
// Do not change something in here.

return ".var_export($lockNameSpaces, true).";");

        file_put_contents("uppm.locks.json", $this->json());
    }

    private function addRec($dir, $key, &$lockNameSpaces){
        foreach (scandir($dir) as $f) {
            if ($f != ".." && $f != ".") {
                if (is_dir($dir."/".$f)) {
                    $this->addRec($dir . "/" . $f, $key, $lockNameSpaces);
                } else {
                    $class = str_replace(".php", "", $f);
                    $lockNameSpaces[$key . "\\" . $class] = $dir . "/" . $f;
                }
            }
        }
    }
}