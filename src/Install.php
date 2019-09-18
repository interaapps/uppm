<?php
/**
 * - ULOLEPHPPACKAGEMANAGER -
 * 
 * Install
 * 
 * @author InteraApps
 */

 class Install {

    private $downloadUrl,
            $type,
            $enddir,
            $webContext,
            $name,
            $version;

    public function __construct($name, $version, $output=true) {
        $this->name    = $name;
        $this->version = $version;
        if ($output) if ($output) Tools::statusIndicator(0, 100);
        if ($version == ":github"){
            $this->webContext = stream_context_create([
                "http" => [
                    "method" => "GET",
                    "header" => "User-Agent: request"
                ]
            ]);
            $branch = "master";
            $this->type = "web";
            if (strpos($name, "+") !== false) {
                $branch = ("+".Tools::getStringBetween($name, "+", ""));
                $this->name= str_replace( $branch, "", $name);
            }
            $this->downloadUrl = "https://api.github.com/repos/".$this->name."/zipball/".str_replace("+", "", $branch);
        } elseif($version == ":web") {
            $this->type = "web";
            $this->downloadUrl = $name;
        } elseif(UPPMINFO["server"] !== false) {
            $list = @json_decode(@file_get_contents((UPPMINFO["server"])));
            if ($list->{$this->name}->{$this->version} != null) {
                $this->downloadUrl = $list->{$this->name}->{$this->version};
            }
            $this->type = "normal";

        }
    }

    public function download($output=true) {
        if ($output) Tools::statusIndicator(5, 100);
        file_put_contents("UPPMtemp_module.zip", file_get_contents($this->downloadUrl, false, $this->webContext));
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive;
            if ($output) Tools::statusIndicator(10, 100);
            $res = $zip->open("UPPMtemp_module.zip");
            if ($res === true) {
                if ($output) Tools::statusIndicator(20, 100);
                Tools::deleteDir("UPPMtempdir");
                if ($output) Tools::statusIndicator(25, 100);
                $zip->extractTo("UPPMtempdir");
                if ($output) Tools::statusIndicator(30, 100);
                $zip->close();

                $files = scandir('UPPMtempdir');
                $dirInZip = false;

                $count = (function($files) {
                    $counter = 0;
                    foreach ($files as $f)
                        if ($f != "." && $f != "..")
                            $counter++;
                    return $counter;
                })($files);

                if (file_exists("UPPMtempdir/uppm.json"))
                    $tempuppmconf = json_decode(file_get_contents("UPPMtempdir/uppm.json"));

                if ($count == 1) {
                    foreach($files as $file) {
                        if (is_dir("UPPMtempdir/".$file)) {
                            if ($file != "." && $file != "..") {
                                $dirInZip = $file;
                                if (file_exists("UPPMtempdir/".$file."/uppm.json"))
                                    $tempuppmconf = json_decode(file_get_contents("UPPMtempdir/".$file."/uppm.json"));
                            }
                        }
                    }
                }

                if ($output) Tools::statusIndicator(50, 100);

                if (isset($tempuppmconf->directory))
                    $enddir = $tempuppmconf->directory;
                elseif (isset($tempuppmconf->name))
                    $enddir = "modules/".$tempuppmconf->name;
                if (is_dir($enddir) && $enddir!="./" )
                    Tools::deleteDir($enddir);
                
                if (!is_dir("modules"))
                    mkdir("modules");

                $copy = false;

                if ($output && (isset($tempuppmconf->directory) ? $tempuppmconf->directory : "") == "./") {
                    echo "\nThis module will be moved to this directory: ".dirname(__FILE__)." Do you want that? [yes,NO]";
                    if (strtolower(readline()) != "yes")
                        die("Cancelt");
                    $copy = true;
                    $enddir = dirname(__FILE__);
                }

                if ($dirInZip !== false) {
                    if ($copy)
                        Tools::copyDir("UPPMtempdir/".$dirInZip, $enddir);
                    else
                        rename("UPPMtempdir/".$dirInZip, $enddir);
                } else {
                    if ($copy)
                        Tools::copyDir("UPPMtempdir", $enddir);
                    else
                        rename("UPPMtempdir", $enddir);
                }

                if (isset($tempuppmconf->modules)) {
                    foreach ($tempuppmconf->modules as $name=>$version) {
                        $resource = new Install($name, $version);
                        $resource->download();
                    }
                }

                if ($output) Tools::statusIndicator(60, 100);

                $lockFile = Configs::getLockFile();
                if (is_array($lockFile->packages) || $lockFile->packages == null) {
                    $lockFile->packages = ["TEMPNULL-------"=>"TEMPNULL-------"];
                }
                $lockFile->packages->{$this->version} = $this->name;
                if (isset($tempuppmconf)) {

                    if (isset($tempuppmconf->directnamespaces)) {
                        if (is_array($tempuppmconf->directnamespaces)) {
                            $tempuppmconf->directnamespaces = ["TEMPNULL-------"=>"TEMPNULL-------"];
                        }

                        foreach ($tempuppmconf->directnamespaces as $key => $val)
                            $lockFile->directnamespaces->{$key} = $val;
                    }
                    if (isset($tempuppmconf->cli_scripts)) {
                        if (is_array($tempuppmconf->cli_scripts)) {
                            $tempuppmconf->cli_scripts = ["TEMPNULL-------"=>"TEMPNULL-------"];
                        }

                        foreach ($tempuppmconf->cli_scripts as $key => $val)
                            $lockFile->cli_scripts->{$key} = $val;
                    }
                }

                rmdir("UPPMtempdir");
                unlink("UPPMtemp_module.zip");

                if ($output) Tools::statusIndicator(80, 100);
                file_put_contents("uppm.locks.json", json_encode($lockFile, JSON_PRETTY_PRINT));
                if ($output) Tools::statusIndicator(100, 100);
                echo "Done\n";
            }
        }
    }

    public static function installNew($name) {
        if (strpos($name, ":") !== false) {
            $type = Tools::getStringBetween($name, "", ":");
            $name = Tools::getStringBetween($name, ":", "");
            $config = Configs::getNPPMFile();
            if ($type=="github") {
                if (is_array($config->modules))
                    $config->modules = [$name=>":github"];
                else
                    $config->modules->{$name} = ":github";
                file_put_contents("uppm.json", json_encode($config, JSON_PRETTY_PRINT));
                (new Install($name, ":github"))->download();
            } elseif ($type=="web") {
                if (is_array($config->modules))
                    $config->modules = [$name=>":web"];
                else
                    $config->modules->{$name} = ":web";
                file_put_contents("uppm.json", json_encode($config, JSON_PRETTY_PRINT));
                (new Install($name, ":web"))->download();
            }
        }
    }

 }

 ?>