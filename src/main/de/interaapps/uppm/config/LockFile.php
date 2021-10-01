<?php
namespace de\interaapps\uppm\config;

use de\interaapps\uppm\helper\JSONModel;

class LockFile {
    use JSONModel;

    public object|null $namespaceBindings;
    public object|null $directNamespaceBindings;
    public object|null $modules;

    public function __construct() {
        $this->namespaceBindings = (object)[];
        $this->directNamespaceBindings = (object)[];
        $this->modules = (object)[];
    }

    public function save(){
        file_put_contents("uppm.locks.json", $this->json());
    }
}