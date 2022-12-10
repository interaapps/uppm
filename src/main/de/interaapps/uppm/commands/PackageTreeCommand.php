<?php
namespace de\interaapps\uppm\commands;

use de\interaapps\ulole\core\cli\Colors;
use de\interaapps\uppm\config\Configuration;

class PackageTreeCommand extends Command {
    public function execute(array $args) {
        $this->printOut($this->uppm->getCurrentProject()->getConfig());
    }

    private function printOut(Configuration $config, $indent = '', $prefix = '📁') {
        $this->uppm->getLogger()->log("{$indent}{$prefix} §h{$config->name} §g{$config->version}§f");

        $i = 0;
        foreach ($config->modules as $module => $version) {
            $i++;
            $modConfig = Configuration::fromFile($this->uppm->getCurrentDir() . "/modules/$module/uppm.json");

            if ($modConfig != null) {
                $this->printOut($modConfig, "$indent   ", '📦');
            }
        }
    }
}