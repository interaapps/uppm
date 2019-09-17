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
            $webContext;

    public function __construct($name, $version) {

        if ($name == ":github"){
            $this->webContext = stream_context_create([
                "http" => [
                    "method" => "GET",
                    "header" => "User-Agent: request"
                ]
            ]);
            $branch = "master";
            $type = "web";
            if (strpos($version, ">") !== false) {
                $branch = (">".Tools::getStringBetween($version, ">", ""));
                $version = str_replace( $branch, "", $version);
            }
            $this->downloadUrl = "https://api.github.com/repos/".$version."/zipball/".$branch;    
        } elseif($name == ":web") {
            $type = "web";
            $this->downloadUrl = $version;
        } else {
            $this->type = "normal";
        }
    }

    public function download() {
        echo "\nDownloading (This may take a while)...\n";
        file_put_contents("UPPMtemp_module.zip", file_get_contents($this->downloadUrl, false, $this->webContext));
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive;
            $res = $zip->open("UPPMtemp_module.zip");
            if ($res === true) {
                Tools::deleteDir("UPPMtempdir");
                $zip->extractTo("UPPMtempdir");
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

                if (isset($tempuppmconf->directory))
                    $enddir = $tempuppmconf->directory;
                elseif (isset($tempuppmconf->name))
                    $enddir = "modules/".$tempuppmconf->name;
                if (is_dir($enddir))
                    Tools::deleteDir($enddir);
                
                if (!is_dir("modules"))
                    mkdir("modules");

                if ($dirInZip !== false) {
                    rename("UPPMtempdir/".$dirInZip, $enddir);
                } else
                    rename("UPPMtempdir", $enddir);
                

                echo "Done";
            }
        }
    }

 }

 ?>