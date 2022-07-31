<?php
namespace de\interaapps\uppm;


use de\interaapps\uppm\config\Configuration;
use de\interaapps\uppm\config\LockFile;
use de\interaapps\uppm\package\Package;

class Project {

    public function __construct(private string $dir,
                                private Configuration $config,
                                private LockFile $lockFile) {
    }

    public function getConfig(): Configuration {
        return $this->config;
    }

    public function getDir(): string {
        return $this->dir;
    }

    public function getLockFile(): LockFile{
        return $this->lockFile;
    }
}