<?php
namespace de\interaapps\uppm\commands;

use de\interaapps\uppm\config\Configuration;
use de\interaapps\uppm\UPPM;

class LockCommand extends Command {
    public function execute(array $args) {
        $this->uppm->getCurrentProject()->getConfig()->lock($this->uppm, $this->uppm->getCurrentProject()->getLockFile(), ".");

        foreach (scandir($this->uppm->getCurrentDir()."/modules") as $module) {
            if ($module != "." && $module != ".." && is_dir($this->uppm->getCurrentDir()."/modules/".$module)) {
                $file = $this->uppm->getCurrentDir()."/modules/$module/uppm.json";
                if (file_exists($file)){
                    $mod = Configuration::fromJson(file_get_contents($file));
                    $mod->lock($this->uppm, $this->uppm->getCurrentProject()->getLockFile(), "modules/$module");
                }
            }
        }
    }
}