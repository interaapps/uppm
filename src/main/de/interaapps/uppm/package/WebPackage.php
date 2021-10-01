<?php
namespace de\interaapps\uppm\package;

use de\interaapps\uppm\helper\Web;

class WebPackage extends Package {
    public function getDownloadURL() : string|null {
        return $this->getName();
    }
}