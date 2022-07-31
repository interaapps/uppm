<?php

namespace de\interaapps\uppm\package;

class WebPackage extends Package {
    public function getDownloadURL(): string|null {
        return $this->getName();
    }
}