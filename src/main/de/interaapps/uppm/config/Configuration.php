<?php

namespace de\interaapps\uppm\config;


use de\interaapps\jsonplus\JSONModel;
use de\interaapps\uppm\UPPM;

class Configuration {
    use JSONModel;

    public string $name = "";
    public string $version = "";
    public string|null $phpVersion = null;

    public array $repositories = [];

    public object|null $run;
    public object|null $build;
    public object|null $serve;

    public object|null $modules;

    public object|null $namespaceBindings;
    public array|null $initScripts;
    public object|null $directNamespaceBindings;
    public object|null $packages;


    // Backwards-Compatibility
    public object|null $namespace_bindings;
    public object|null $directnamespaces;

    public function __construct() {
        $this->modules = (object)[];
        $this->build = (object)[];
        $this->serve = (object)[];
        $this->run = (object)[];
        $this->namespaceBindings = (object)[];
        $this->initScripts = [];
    }

    public function save($uppm): void {
        if (($key = array_search("https://central.uppm.interaapps.de", $this->repositories)) !== false)
            unset($this->repositories[$key]);
        file_put_contents($uppm->getCurrentDir() . "/uppm.json", $this->toJson());
    }

    public function lock(UPPM $uppm, LockFile $lockFile, $folderPrefix = ""): void {
        if (isset($this->namespace_bindings)) {
            foreach ($this->namespace_bindings as $name => $v) {
                $this->namespaceBindings->{$name} = $v;
            }
        }

        if (isset($this->namespace_bindings))
            $this->namespaceBindings = isset($this->namespaceBindings) ? (object)array_merge((array)$this->namespaceBindings, (array)$this->namespace_bindings) : $this->namespace_bindings;

        if (isset($this->namespaceBindings)) {
            foreach ($this->namespaceBindings as $name => $v) {
                if (str_ends_with($name, "\\")) {
                    unset($this->namespaceBindings->{$name});
                    $name = substr($name, 0, strlen($name) - 1);
                }
                if (is_array($v))
                    $v = $v[0];
                if (str_ends_with($v, "/"))
                    $v = substr($v, 0, strlen($v) - 1);
                $this->namespaceBindings->{$name} = $folderPrefix . "/" . $v;
            }
        }

        if (isset($this->initScripts)) {
            foreach ($this->initScripts as $f) {
                $this->initScripts[] = $folderPrefix . "/" . $f;
            }
        }

        if (isset($this->initScripts))
            $lockFile->initScripts = array_unique(array_merge((array)$lockFile->initScripts, (array)$this->initScripts));

        if (isset($this->directNamespaceBindings))
            $lockFile->directNamespaceBindings = (object)array_merge((array)$lockFile->directNamespaceBindings, (array)$this->directNamespaceBindings);

        // Backwards Compatibility
        if (isset($this->directnamespaces))
            $lockFile->directNamespaceBindings = (object)array_merge((array)$lockFile->directNamespaceBindings, (array)$this->directnamespaces);

        if (isset($this->namespaceBindings))
            $lockFile->namespaceBindings = (object)array_merge((array)$lockFile->namespaceBindings, (array)$this->namespaceBindings);

        $lockFile->modules = (object)array_merge((array)$lockFile->modules, [$this->name => $this->version]);
        $lockFile->save($uppm);
    }
}