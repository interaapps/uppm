<?php
namespace de\interaapps\uppm\package;

class GithubPackage extends Package {
    public function getDownloadURL() : string|null {
        $split = explode("+", $this->getName());
        return "https://api.github.com/repos/".$split[0]."/zipball/".(count($split) > 1 ? $split[1] : '');
    }


    public function addToConfig(){
        $config = $this->uppm->getCurrentProject()->getConfig();
        $config->modules->{$this->name."@github"} = $this->version;
        $config->save();
    }
}