<?php
namespace de\interaapps\uppm\package;

use de\interaapps\jsonplus\JSONModel;
use de\interaapps\jsonplus\JSONPlus;
use de\interaapps\uppm\config\Configuration;
use de\interaapps\uppm\helper\Web;
use de\interaapps\uppm\UPPM;

class PackagistVersionReq {
    public string $version = "";
    public string $type = "";
    public object $dist;
}
class PackagistPackageReq {
    use JSONModel;
    public string $name;
    public object $versions;
}

class PackagistPackageWrReq {
    use JSONModel;
    public PackagistPackageReq $package;
}

class PackagistPackage extends Package {
    public function getDownloadURL() : string|null {
        $split = explode(":", $this->getName());
        $name = $split[0];
        $downloadUrl = null;
        try {
            $package = PackagistPackageWrReq::fromJson(Web::httpRequest("https://packagist.org/packages/$name.json"))->package;
            foreach ($package->versions as $name=>$version) {
                $version = $this->uppm->getJsonPlus()->map($version, PackagistVersionReq::class);
                $v = "";
                if (count($split) > 1)
                    $v = $split[1];

                if (str_contains($v, "||")) {
                    $s = explode("||", $v);
                    $v = trim($s[count($s)-1]);
                }
                if (
                    (($v == "") || $version->version == $v)
                 || (str_contains($v, "^") && str_starts_with(str_replace("^", "", $v), $version->version))
                ) {
                    if ($version->dist !== null) {
                        if (isset($version->version) && $version->version != "")
                            $this->version = $version->version;
                        $downloadUrl = $version->dist->url;
                        break;
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->uppm->getLogger()->err("An download error occured: ".$exception->getMessage());
        }

        return $downloadUrl;
    }

    public function getConfig(string $dir): Configuration {
        if (file_exists("$dir/composer.json")) {
            $f = (object) $this->uppm->getJsonPlus()->fromJson(file_get_contents("$dir/composer.json"), "object");
            $conf = new Configuration();
            $conf->name = $f->name;
            if (isset($f->version))
                $conf->version = $f->version;
            else
                $conf->version = $this->version;
            $conf->namespaceBindings = (object) [];
            $conf->modules = (object) [];
            if (isset($f->require)) {
                foreach ($f->require as $name=>$ver) {
                    if ($name != "php" && !str_starts_with($name, "ext-"))
                        $conf->modules->{$name."@composer"} = $ver;
                }
            }

            if (isset($f->autoload)) {
                if (isset($f->autoload->{"psr-4"})) {
                    foreach ($f->autoload->{"psr-4"} as $namespace=>$folder) {
                        $conf->namespaceBindings->{$namespace} = $folder;
                    }
                }
                if (isset($f->autoload->files)) {
                    foreach ($f->autoload->files as $file) {
                        $conf->initScripts[] = $file;
                    }
                }
            }

            return $conf;
        }
        return parent::getConfig($dir);
    }


    public function addToConfig(){
        $config = $this->uppm->getCurrentProject()->getConfig();
        $config->modules->{$this->name."@composer"} = $this->version;
        $config->save();
    }
}