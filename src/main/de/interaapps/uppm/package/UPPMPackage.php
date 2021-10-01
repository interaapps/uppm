<?php
namespace de\interaapps\uppm\package;

use de\interaapps\uppm\helper\Web;

class UPPMPackage extends Package {

    public function getDownloadURL() : string|null {
        $list = [];

        foreach ($this->uppm->getCurrentProject()->getConfig()->repositories as $repo) {
            $list = array_merge($list, (array) json_decode(Web::httpRequest($repo."?name=$this->name&version=$this->version")));
        }
        $list = json_decode(json_encode($list));

        if ($this->version == "latest")
            $this->version = $list?->{$this->getName()}?->newest;

        return $list?->{$this->getName()}?->{$this->getVersion()};
    }
}