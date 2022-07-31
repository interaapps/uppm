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

    public function download() : bool {
        $this->uppm->getLogger()->log("\n");
        $this->uppm->getLogger()->log("Installing 📦 §h{$this->name} §g{$this->version}§f");
        $this->uppm->getLogger()->log("");

        $this->uppm->getLogger()->loadingBar(0);

        $downloadUrl = $this->getDownloadURL();
        if ($downloadUrl == null) {
            $this->uppm->getLogger()->err("Can't find package $this->name:$this->version");
            return false;
        }

        $this->uppm->getLogger()->loadingBar(0.2, "Downloading");

        if (!file_exists($this->uppm->getCurrentDir()."/modules/"))
            mkdir($this->uppm->getCurrentDir()."/modules/");

        $cname = str_replace("/", "-", $this->getName());

        $this->uppm->getLogger()->loadingBar(0.4, "Unpacking files");
        [$zipOutDir, $tmpName] = $this->unpack();

        $outputDir = $this->uppm->getCurrentDir()."/modules/".$cname;

        $this->uppm->getLogger()->loadingBar(0.6, "Copying files."); //  $tmpName to $zipOutDir

        if (file_exists($zipOutDir."/uppm.json") || file_exists($zipOutDir."/composer.json")) {
            $this->uppm->getLogger()->loadingBar(0.8, "Adding to lock-file");

            $conf = $this->getConfig($zipOutDir);
            if (isset($conf->name) && $conf->name != "")
                $outputDir = "modules/".str_replace("/", "-", $conf->name);

            $this->uppm->getLogger()->loadingBar(0.9, "Output dir §3$outputDir");

            if (file_exists($this->uppm->getCurrentDir()."/".$outputDir))
                Files::deleteDir($this->uppm->getCurrentDir()."/".$outputDir);

            $conf->lock($this->uppm, $this->uppm->getCurrentProject()->getLockFile(), $outputDir);
            if (isset($conf->modules)) {
                foreach ($conf->modules as $name=>$ver) {
                    if (!isset($this->uppm->getCurrentProject()?->getConfig()?->modules?->{$name})) {
                        if ($conf->phpVersion > phpversion())
                            $this->uppm->getLogger()->warn("The package {$conf->name} requires php version §h".$conf->phpVersion."§f. You are using version §c".phpversion()."§f.");

                        $package = Package::getPackage($this->uppm, $name, $ver);

                        $package->download();
                    }
                }
            }
        }
        Files::copyDir($zipOutDir, $this->uppm->getCurrentDir()."/$outputDir");
        Files::deleteDir($tmpName);

        $this->uppm->getLogger()->loadingBar(1, "Done");

        return true;
    }

    public function unpack(): array {
        $tmpName = sys_get_temp_dir()."/ULOLE-".rand(111111111111, 99999999999);

        file_put_contents("$tmpName.zip", Web::httpRequest($this->getDownloadURL()));
        $zip = new ZipArchive;
        $res = $zip->open("$tmpName.zip");
        if ($res === true) {
            $zip->extractTo($tmpName);
            $zip->close();
        } else {
            $this->uppm->getLogger()->err("Zip from {$this->getDownloadURL()} invalid!");
        }
        $zipOutDir = $tmpName;
        $items = scandir($zipOutDir);
        $items = array_values(array_filter($items, fn($n) => $n != "." && $n != ".."));

        if (count($items) == 1) {
            if (is_dir($zipOutDir."/".$items[0]))
                $zipOutDir .= "/".$items[0];
        }
        return [$zipOutDir, $tmpName];
    }

    public function getConfig(string $dir) : Configuration {
        return $this->uppm->getJsonPlus()->fromJson(file_get_contents("$dir/uppm.json"), Configuration::class);
    }

    public function remove(): void {
        $outputDir = "./modules/$this->name";
        if (file_exists($outputDir))
            Files::deleteDir($outputDir);
    }

    public function addToConfig(): void {
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