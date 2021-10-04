<?php
namespace de\interaapps\uppm\package;

use de\interaapps\uppm\config\Configuration;
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

    public function download() : void {
        $tmpName = sys_get_temp_dir()."/"."ULOLE-".rand(111111111111, 99999999999);
        $downloadUrl = $this->getDownloadURL();
        $this->uppm->getLogger()->info("Downloading from $downloadUrl");
        file_put_contents($tmpName.".zip", Web::httpRequest($downloadUrl));
        $zip = new ZipArchive;
        $res = $zip->open($tmpName.".zip");
        if ($res === TRUE) {
            $zip->extractTo($tmpName);
            $zip->close();
        } else {
            $this->uppm->getLogger()->err("Zip from $downloadUrl invalid!");
        }
        if (!file_exists(getcwd()."/modules/"))
            mkdir(getcwd()."/modules/");

        $this->uppm->getLogger()->info("OUTPUT DIR ./modules/".$this->name);
        $cname = str_replace("/", "-", $this->getName());


        $zipOutDir = $tmpName;

        $outputDir = getcwd()."/modules/".$cname;

        $items = scandir($zipOutDir);
        $items = array_values(array_filter($items, fn($n)=>$n != "." && $n != ".."));

        if (count($items) == 1) {
            if (is_dir($zipOutDir."/".$items[0]))
                $zipOutDir .= "/".$items[0];
        }
        $this->uppm->getLogger()->info("Copying $tmpName to $zipOutDir.");

        if (file_exists($zipOutDir."/uppm.json")) {
            $this->uppm->getLogger()->info("Adding to lock-file");
            $conf = Configuration::fromJson(file_get_contents($zipOutDir."/uppm.json"));
            if (isset($conf->name) && $conf->name != "")
                $outputDir = "modules/".str_replace("/", "-", $conf->name);

            if (file_exists(getcwd()."/".$outputDir))
                Files::deleteDir(getcwd()."/".$outputDir);

            $conf->lock($this->uppm->getCurrentProject()->getLockFile(), $outputDir);
            if (isset($conf->modules)) {
                foreach ($conf->modules as $name=>$ver) {
                    if (!isset($this?->uppm?->getCurrentProject()?->getConfig()?->modules?->{$name})) {
                        $package = Package::getPackage($this->uppm, $name, $ver);
                        $package->download();
                    }
                }
            }
        }
        Files::copyDir($zipOutDir, getcwd()."/".$outputDir);
        Files::deleteDir($tmpName);
    }

    public function remove(){
        $outputDir = "./modules/".$this->name;
        if (file_exists($outputDir))
            Files::deleteDir($outputDir);
    }

    public function addToConfig(){
        $config = $this->uppm->getCurrentProject()->getConfig();
        $config->modules->{$this->name} = $this->version;
        $config->save();
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

        $split = explode("@", $gname);
        $name = $split[0];

        if (count($split) > 1)
            $source = $split[1];

        switch ($source) {
            case "uppm":
                return new UPPMPackage($uppm, $name, $version);
            case "github":
                return new GithubPackage($uppm, $name, $version);
            case "web":
                return new WebPackage($uppm, $name, $version);
        }
        return null;
    }
}