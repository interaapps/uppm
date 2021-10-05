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
    }

    public function save() : void {
        if (($key = array_search("https://central.uppm.interaapps.de", $this->repositories)) !== false)
            unset($this->repositories[$key]);
        file_put_contents(getcwd()."/uppm.json", $this->json());
    }

    public function lock($lockFile, $folderPrefix="") : void {
        if (isset($this->namespaceBindings))
            foreach ($this->namespaceBindings as $name=>$v)
                $this->namespaceBindings->{$name} = $folderPrefix . "/" . $v;
        if (isset($this->namespace_bindings))
            foreach ($this->namespace_bindings as $name=>$v)
                $this->namespace_bindings->{$name} = $folderPrefix . "/" . $v;

        if (isset($this->directNamespaceBindings))
            $lockFile->directNamespaceBindings = (object) array_merge((array) $lockFile->directNamespaceBindings, (array) $this->directNamespaceBindings);
        if (isset($this->namespaceBindings))
            $lockFile->namespaceBindings = (object) array_merge((array) $lockFile->namespaceBindings, (array) $this->namespaceBindings);

        // Backwards Compatibility

        if (isset($this->directnamespaces))
            $lockFile->directNamespaceBindings = (object) array_merge((array) $lockFile->directNamespaceBindings, (array) $this->directnamespaces);
        if (isset($this->namespace_bindings))
            $lockFile->namespaceBindings = (object) array_merge((array) $lockFile->namespaceBindings, (array) $this->namespace_bindings);

        $lockFile->modules = (object) array_merge((array) $lockFile->modules, [$this->name => $this->version]);
        $lockFile->save();
    }
}