<?php
namespace de\interaapps\uppm\package;

use de\interaapps\uppm\config\Configuration;
use de\interaapps\uppm\config\LockFile;
use de\interaapps\uppm\helper\Files;
use de\interaapps\uppm\helper\Web;
use de\interaapps\uppm\UPPM;
use ZipArchive;

abstract class Package {
    public object|null $namespaceBindings;
    public object|null $directNamespaceBindings;
    public object|null $packages;

    public function __construct(
        protected UPPM $uppm,
        protected $name,
        protected $version,
    ) {
    }

    public function download() : bool {
        $downloadUrl = $this->getDownloadURL();
        if ($downloadUrl == null) {
            $this->uppm->getLogger()->err("Can't find package $this->name:$this->version");
            return false;
        }
        $this->uppm->getLogger()->info("Downloading from $downloadUrl");

        if (!file_exists($this->uppm->getCurrentDir()."/modules/"))
            mkdir($this->uppm->getCurrentDir()."/modules/");

        $cname = str_replace("/", "-", $this->getName());

        [$zipOutDir, $tmpName] = $this->unpack();

        $outputDir = $this->uppm->getCurrentDir()."/modules/".$cname;

        $this->uppm->getLogger()->info("Copying $tmpName to $zipOutDir.");

        if (file_exists($zipOutDir."/uppm.json") || file_exists($zipOutDir."/composer.json")) {
            $this->uppm->getLogger()->info("Adding to lock-file");
            $conf = $this->getConfig($zipOutDir);
            if (isset($conf->name) && $conf->name != "")
                $outputDir = "modules/".str_replace("/", "-", $conf->name);

            $this->uppm->getLogger()->info("OUTPUT DIR ./modules/".$outputDir);

            if (file_exists($this->uppm->getCurrentDir()."/".$outputDir))
                Files::deleteDir($this->uppm->getCurrentDir()."/".$outputDir);

            $conf->lock($this->uppm, $this->uppm->getCurrentProject()->getLockFile(), $outputDir);
            if (isset($conf->modules)) {
                foreach ($conf->modules as $name=>$ver) {
                    if (!isset($this?->uppm?->getCurrentProject()?->getConfig()?->modules?->{$name})) {
                        $package = Package::getPackage($this->uppm, $name, $ver);
                        $package->download();
                    }
                }
            }
        }
        Files::copyDir($zipOutDir, $this->uppm->getCurrentDir()."/".$outputDir);
        Files::deleteDir($tmpName);
        return true;
    }

    public function unpack(): array {
        $tmpName = sys_get_temp_dir()."/"."ULOLE-".rand(111111111111, 99999999999);

        file_put_contents($tmpName.".zip", Web::httpRequest($this->getDownloadURL()));
        $zip = new ZipArchive;
        $res = $zip->open($tmpName.".zip");
        if ($res === TRUE) {
            $zip->extractTo($tmpName);
            $zip->close();
        } else {
            $this->uppm->getLogger()->err("Zip from {$this->getDownloadURL()} invalid!");
        }
        $zipOutDir = $tmpName;
        $items = scandir($zipOutDir);
        $items = array_values(array_filter($items, fn($n)=>$n != "." && $n != ".."));

        if (count($items) == 1) {
            if (is_dir($zipOutDir."/".$items[0]))
                $zipOutDir .= "/".$items[0];
        }
        return [$zipOutDir, $tmpName];
    }

    public function getConfig(string $dir) : Configuration {
        return Configuration::fromJson(file_get_contents($dir."/uppm.json"));
    }

    public function remove(){
        $outputDir = "./modules/".$this->name;
        if (file_exists($outputDir))
            Files::deleteDir($outputDir);
    }

    public function addToConfig(){
        $config = $this->uppm->getCurrentProject()->getConfig();
        $config->modules->{$this->name} = $this->version;
        $config->save($this->uppm);
    }

    public abstract function getDownloadURL() : string|null;

    public function getName(){
        return $this->name;
    }

    public function getVersion() {
        return $this->version;
    }

    public static function getPackage($uppm, $gname, $version) : Package|null {
        $source = "uppm";

        if (str_contains($version,"@")) {
            $split = explode("@", $version);
            $version = $split[0];
            $name = $gname;

            if (count($split) > 1)
                $source = $split[1];
        } else {
            $split = explode("@", $gname);
            $name = $split[0];

            if (count($split) > 1)
                $source = $split[1];
        }
        switch ($source) {
            case "uppm":
                return new UPPMPackage($uppm, $name, $version);
            case "github":
                return new GithubPackage($uppm, $name, $version);
            case "packagist":
            case "composer":
                return new PackagistPackage($uppm, $name, $version);
            case "web":
                return new WebPackage($uppm, $name, $version);
        }
        return null;
    }
}