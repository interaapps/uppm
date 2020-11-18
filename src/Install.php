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
            $enddir = false,
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
            $list = @json_decode(@file_get_contents((UPPMINFO["server"])), true);
            global $uppmconf;
            if (isset($uppmconf) && isset($uppmconf->repositories)) {
                foreach ($uppmconf->repositories as $repository => $link) {
                    $list = array_merge($list, @json_decode(@file_get_contents($link,false, stream_context_create([ "http" => [ "method" => "GET", "header" => "User-Agent: request" ] ])), true));
                }
            }
            $list = json_decode(json_encode($list));
            if ($list->{$this->name}->{$this->version} != null) {
                $this->downloadUrl = $list->{$this->name}->{$this->version};
            }
            $this->type = "normal";
        }
    }

    public function download($output=true) {
        global $uppmconf;
        $enddir = "modules/cbf_".rand(0000,9999); // cbf = Cant be fetched
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
                        if (is_dir("UPPMtempdir/".$file) && $file != "." && $file != "..") {
                            $dirInZip = $file;
                            if (file_exists("UPPMtempdir/".$file."/uppm.json"))
                                $tempuppmconf = json_decode(file_get_contents("UPPMtempdir/".$file."/uppm.json"));
                        }
                    }
                }

                if ($output)
                    Tools::statusIndicator(50, 100);


                if (isset($tempuppmconf->directory)){
                    $enddir = $tempuppmconf->directory;
                } elseif (isset($tempuppmconf->name)){
                    $enddir = "modules/".$tempuppmconf->name;
                }
                
                if (is_dir($enddir) && $enddir!="./" ){
                    Tools::deleteDir($enddir);
                }

                if (!is_dir("modules"))
                    mkdir("modules");

                $copy = false;

                if ($output && (isset($tempuppmconf->directory) ? $tempuppmconf->directory : "") == "./") {
                    echo "\nThis module will be moved to this directory: ".getcwd()." Do you want that? [yes,NO] ";
                    if (strtolower(readline()) != "yes" && strtolower(readline()) != "y")
                        die("Cancelled");
                    $copy = true;
                    $enddir = getcwd()."/";
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
                    $config = Configs::getNPPMFile();
                    foreach ($tempuppmconf->modules as $name=>$version) {
                        if (!isset($uppmconf->{$name})) {
                            Colors::info("Installing dependency $name $version");
                            (new Install($name, $version))->download();
                            Colors::done("Installed dependency $name");

                            Colors::info("Adding to uppm.json (modules)");
                            $config->modules->{$name} = $version;
                        } else if ($uppmconf->{$name} != $version) {
                            Colors::info("The dependency $name is already installed with the version ".$uppmconf->{$name}." but the dependency $tempuppmconf->name needs the version $version. Do you want to change the dependencies version? yes/NO");
                            if (readline("") == "yes" || readline("") == "y") {
                                (new Install($name, $version))->download();
                                Colors::done("Installed dependency $name");
                                Colors::info("Adding to uppm.json (modules)");
                                $config->modules->{$name} = $version;
                            }
                        } else
                            Colors::info("Dependency $name $version is installed");
                    }
                    file_put_contents("uppm.json", json_encode($config, JSON_PRETTY_PRINT));
                }

                if ($output) Tools::statusIndicator(60, 100);
                
                Colors::info("Writing $tempuppmconf->name information (Namespaces, cli-scripts...) into locks.");
                Tools::lockFile($tempuppmconf);

                rmdir("UPPMtempdir");
                unlink("UPPMtemp_module.zip");

                if ($output) Tools::statusIndicator(80, 100);

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
            
        } else {
            global $uppmconf;

            $list = @json_decode(@file_get_contents((UPPMINFO["server"])));

            if (isset($uppmconf) && isset($uppmconf->repositories)) {
                foreach ($uppmconf->repositories as $repository => $link)
                    $list = array_merge($list, @json_decode(@file_get_contents($link,false, stream_context_create([ "http" => [ "method" => "GET", "header" => "User-Agent: request" ] ])), true));
            }

            if (strpos($name, "@") !== false) {
                $version = Tools::getStringBetween($name, "@", "");
                $name = Tools::getStringBetween($name, "", "@");
            } elseif (isset($list->{$name}->newest)) {
                $version = $list->{$name}->newest;
            } else {
                echo "Version not found!";
                return "\n";
            }
    
            if (isset($list->{$name}->{$version})) {
                $config = Configs::getNPPMFile();
                if (is_array($config->modules))
                    $config->modules = [$name=>$version];
                else
                    $config->modules->{$name} = $version;
                
                file_put_contents("uppm.json", json_encode($config, JSON_PRETTY_PRINT));
                (new Install($name, $version))->download();
            } else {
                echo "Package not found";
            }
        }
    }

 }

 ?>