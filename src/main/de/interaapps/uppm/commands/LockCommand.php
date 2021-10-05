<?php
namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\config\Configuration;
use de\interaapps\uppm\UPPM;

class LockCommand implements Command {
    public function __construct(private UPPM $uppm) {
    }

    public function execute(array $args) {
        $this->uppm->getCurrentProject()->getConfig()->lock($this->uppm->getCurrentProject()->getLockFile(), ".");

        foreach (scandir("modules") as $module) {
            if ($module != "." && $module != ".." && is_dir($module)) {
                $file = getcwd()."/modules/$module/uppm.json";
                if (file_exists($file)){
                    $mod = Configuration::fromJson(file_get_contents($file));
                    if (key_exists($mod->name, (array)$this->uppm->getCurrentProject()->getConfig()->modules)) {
                        $mod->lock($this->uppm->getCurrentProject()->getLockFile(), "./modules/$module");
                    }
                }
            }
        }
    }
}