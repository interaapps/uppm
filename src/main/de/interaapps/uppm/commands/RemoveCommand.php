<?php
namespace de\interaapps\uppm\commands;

class RemoveCommand extends Command {
    public function execute(array $args) {
        $this->uppm->getLogger()->loadingBar(0, "Removing package...");
        $project = $this->uppm->getCurrentProject();


        if (count($args) > 0) {
            foreach ($args as $arg) {
                $split = explode(":", $arg);

                unset($project->getConfig()->modules->{$split[0]});
                $project->getConfig()->save($this->uppm);
            }
        }
    }
}