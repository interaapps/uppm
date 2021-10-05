<?php
namespace de\interaapps\uppm\package;

use de\interaapps\jsonplus\JSONPlus;
use de\interaapps\uppm\helper\Logger;
use de\interaapps\uppm\helper\Web;
use de\interaapps\uppm\package\uppm\models\PackageVersionResponse;

class UPPMPackage extends Package {

    public function getDownloadURL() : string|null {
        foreach ($this->uppm->getCurrentProject()->getConfig()->repositories as $repo) {
            $name = $this->getName();
            if (!str_contains($name, "/"))
                $name = "_/$name";

            $versionName = $this->version == "latest" ? "@latest" : $this->version;

            $version = PackageVersionResponse::fromJson(Web::httpRequest("$repo/$name/$versionName"));
            if ($version->error)
                continue;
            return $version->download_url;
        }
        return null;
    }
}