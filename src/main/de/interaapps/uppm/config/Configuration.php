<?php
namespace de\interaapps\uppm\config;


use de\interaapps\uppm\helper\JSONModel;

class Configuration {
    use JSONModel;

    public string $name = "";
    public string $version = "";
    public string|null $phpVersion;

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

    public function save() : void {
        if (($key = array_search("https://central.uppm.interaapps.de", $this->repositories)) !== false)
            unset($this->repositories[$key]);
        file_put_contents(getcwd()."/uppm.json", $this->json());
    }

    public function lock(LockFile $lockFile, $folderPrefix="") : void {
        if (isset($this->namespace_bindings)) {
            foreach ($this->namespace_bindings as $name => $v) {
                $this->namespaceBindings->{$name} = $v;
            }
        }

        if (isset($this->namespaceBindings)) {
            foreach ($this->namespaceBindings as $name => $v) {
                if (str_ends_with($name, "\\")) {
                    unset($this->namespaceBindings->{$name});
                    $name = substr($name, 0, strlen($name) - 1);
                }
                if (is_array($v))
                    $v = $v[0];
                if (str_ends_with($v, "/"))
                    $v = substr($v, 0, strlen($v)-1);
                $this->namespaceBindings->{$name} = $folderPrefix . "/" . $v;
            }
        }

        if (isset($this->initScripts)) {
            foreach ($this->initScripts as $f) {
                $this->initScripts[] = $folderPrefix . "/" . $f;
            }
        }

        if (isset($this->directNamespaceBindings))
            $lockFile->directNamespaceBindings = (object) array_merge((array) $lockFile->directNamespaceBindings, (array) $this->directNamespaceBindings);
        if (isset($this->namespaceBindings))
            $lockFile->namespaceBindings = (object) array_merge((array) $lockFile->namespaceBindings, (array) $this->namespaceBindings);

        if (isset($this->initScripts))
            $lockFile->initScripts = array_unique(array_merge((array) $lockFile->initScripts, (array) $this->initScripts));

        // Backwards Compatibility

        if (isset($this->directnamespaces))
            $lockFile->directNamespaceBindings = (object) array_merge((array) $lockFile->directNamespaceBindings, (array) $this->directnamespaces);
        if (isset($this->namespace_bindings))
            $lockFile->namespaceBindings = (object) array_merge((array) $lockFile->namespaceBindings, (array) $this->namespace_bindings);

        $lockFile->modules = (object) array_merge((array) $lockFile->modules, [$this->name => $this->version]);
        $lockFile->save();
    }
}