<?php
namespace de\interaapps\uppm;

use Phar;
use Exception;
use de\interaapps\uppm\Tools;
use de\interaapps\uppm\cli\CLI;
use de\interaapps\uppm\Configs;
use de\interaapps\uppm\cli\Colors;

define('UPPM_CURRENT_DIRECTORY', getcwd()."/");

if (Phar::running() !== "") {
    spl_autoload_register(function($class) {
        if(file_exists("../../../../modules/".str_replace("\\","/",$class).".php"))
            @include_once "../../../../modules/".str_replace("\\","/",$class).".php";
        // Special stuff
        else if(file_exists("../../../../src/".str_replace("\\","/",$class).".php"))
            @include_once   "../../../../src/".str_replace("\\","/",$class).".php";
    });
} else
    require "autoload.php";

ini_set('phar.readonly',0);

$uppmlock = [];
if (file_exists(UPPM_CURRENT_DIRECTORY."uppm.locks.json"))
    $uppmlock = json_decode(file_get_contents(UPPM_CURRENT_DIRECTORY."uppm.locks.json"));

$uppmconf = (object)[];
if (file_exists(UPPM_CURRENT_DIRECTORY."uppm.json"))
    $uppmconf = json_decode(file_get_contents(UPPM_CURRENT_DIRECTORY."uppm.json"));

$serverInfo = @json_decode(@file_get_contents("https://raw.githubusercontent.com/interaapps/uppm-packages/master/uppm.json?".rand(00000, 99999), false, stream_context_create([
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: request"
    ]
])));

define("UPPMINFO", [
    "version"=>"1.2.0",
    "server"=> (isset($serverInfo->list)) ? $serverInfo->list : false
]);

$CLI = new CLI(1);

$CLI->register("", function() {
    global $CLI;

    echo Colors::TURQUIOUS."
            ╔╗─╔╗╔═══╗╔═══╗╔═╗╔═╗
            ║║─║║║╔═╗║║╔═╗║║║╚╝║║
            ║║─║║║╚═╝║║╚═╝║║╔╗╔╗║
            ║║─║║║╔══╝║╔══╝║║║║║║
            ║╚═╝║║║───║║───║║║║║║
            ╚═══╝╚╝───╚╝───╚╝╚╝╚╝
            A PHP PACKAGE MANAGER
                    BY ".Colors::OKBLUE."INTERAAPPS
            ".Colors::ENDC;

    foreach ($CLI->getCommands() as $command=>$execution)
        echo "\n".Colors::TURQUIOUS.$command.Colors::HEADER.": ".Colors::OKBLUE.$CLI->getDescriptions()[$command].Colors::ENDC;

    return Colors::TURQUIOUS."UPPM Version: ".Colors::OKBLUE.UPPMINFO["version"]."\n".Colors::ENDC;
}, "UPPM");

$CLI->register("help", function() {
    global $CLI;
    return $CLI->getCommands()[""]();
});

$CLI->register("-v", function() {
    return Colors::TURQUIOUS."UPPM Version: ".Colors::OKBLUE.UPPMINFO["version"]."\n".Colors::ENDC;
}, "See UPPMs version");

$CLI->register("init", function() {
    return Init::initFromCLI();
}, "Initializing Project");

$CLI->register("init:fast", function() {
    return Init::initProject("uppm project", "1.0", "", "Me", []);
}, "Initializing Project without any information");

$CLI->register("install", function() {
    global $argv, $uppmconf;
    if (count($argv) == 2) {
        Colors::info("Reinstalling packages...");
        $lockFile = Configs::getLockFile();
        $lockFile->packages = ["TEMPNULL-------"=>"TEMPNULL-------"];
        file_put_contents(UPPM_CURRENT_DIRECTORY."uppm.locks.json", json_encode($lockFile, JSON_PRETTY_PRINT));
        foreach ($uppmconf->modules as $name=>$version) {
            $resource = new Install($name, $version);
            $resource->download();
        }
    } elseif(count($argv) == 3) {
        return Install::installNew($argv[2]);
    } else
        Colors::error("Argument error! uppm install or uppm install <package>");
}, "uppm install <package> Install a new package
    Types:
      - 'github:' Downloads a project from github (Example: 'user/project' or 'user/project:master')
      - 'web:' Downloads a project from web (Example: https://mywebsite.com/test.zip)
      - 'local:' Unzips a local file 'file.zip'
     uppm install
        Installs every package");

$CLI->register("update", function() {
    global $uppmconf;
    Colors::info("Fetching main repository");
    $list = @json_decode(@file_get_contents((UPPMINFO["server"])));
    if (isset($uppmconf) && isset($uppmconf->repositories)) {
        foreach ($uppmconf->repositories as $repository => $link) {
            Colors::info("Fetching $repository repository from $link");
            $list = array_merge($list, @json_decode(@file_get_contents($link, false, stream_context_create(["http" => ["method" => "GET", "header" => "User-Agent: request"]])), true));
        }
    }

    Colors::info("Updating packages...");
    $updated = 0;
    foreach ($uppmconf->modules as $name=>$version) {
        if ($version == ":github"){
            Colors::info("Fetching from Github isn't available currently...");
        } elseif($version == ":web") {
            Colors::info("Fetching from web isn't available currently...");
        } elseif($version == ":composer" || $version == ":packagist") {
            Colors::info("Fetching from packagist isn't available currently...");
        } else {
            Colors::info("Checking $name...");

            if (isset($list->{$name}->newest) && $list->{$name}->newest != $version) {
                Colors::info("Updating ".$name." from version ".Colors::RED.$version.Colors::ENDC." to ".Colors::GREEN.$list->{$name}->newest.Colors::ENDC."");
                try {
                    Install::installNew($name);
                } catch (Exception $exception) {
                    Colors::error("Error while updating $name");
                }
                $updated++;
            } else
                Colors::info($name." is up to date");
        }
    }
    Colors::done("Updated $updated packages!");
}, "Updating all");

$CLI->register("linuxglobal", function() {
    Colors::info("This action requires root permissions (sudo)");
    exec("sudo wget --output-document=tmp__uppm https://raw.githubusercontent.com/interaapps/uppm/master/uppm && sudo mv tmp__uppm /usr/local/bin/uppm && sudo chmod 777 /usr/local/bin/uppm");
}, "Installing globally on linux ".Colors::RED."* ".Colors::YELLOW."Root required!");

$CLI->register("serve", function(){
    global $uppmconf;

    $host = "0.0.0.0";
    $port = "8000";
    $directory = ".";
    $routerFile = "";

    if(isset($uppmconf->serve->port)) $port = $uppmconf->serve->port;
    if(isset($uppmconf->serve->host)) $host = $uppmconf->serve->host;
    if(isset($uppmconf->serve->directory)) $directory = $uppmconf->serve->directory;
    if(isset($uppmconf->serve->routerFile)) $routerFile = $uppmconf->serve->routerFile;

    Colors::done("Binding on $port");
    $exec= "cd $directory
    php -S $host:$port -t ./ $routerFile";

    system($exec);
    exec($exec);
    shell_exec($exec);

    Colors::error("Couldn't start the webserver!");

}, "A simple Testserver. It's using the PHPs included one! Please enable exec for it if you didn't.");

$CLI->register("autoload", function(){
    Tools::downloadAutoloader();
});

$CLI->register("run", function() {
    global $uppmconf;
    if ($uppmconf != null) {
        $runner = new Runner();
        if ($uppmconf->run->file != null)
            $runner->setFile($uppmconf->run->file);
        if ($uppmconf->run->arguments != null)
            $runner->setArgs($uppmconf->run->arguments);
        if ($uppmconf->run->exec != null)
            $runner->setExec($uppmconf->run->exec);
        if ($uppmconf->run->compile_first != null)
            $runner->setCompileFirst($uppmconf->run->compile_first);
        $runner->run();
    } else
        Colors::error("Running is not initialized!");

});

$CLI->register("build", function() {
    global $uppmconf;
    $build = new Build();
    $build->setDirectory(".");
    $build->setOutputFile((isset($uppmconf->name)?$uppmconf->name:"project")."-".(isset($uppmconf->version)?$uppmconf->version:"1.0").".phar");

    if(isset($uppmconf->build->main))
        $build->setMain($uppmconf->build->main);
    if(isset($uppmconf->build->output))
        $build->setOutputFile($uppmconf->build->output);
    if(isset($uppmconf->build->src))
        $build->setDirectory($uppmconf->build->src);
    if(isset($uppmconf->build->ignored_directories))
        $build->setIgnoredDirectories($uppmconf->build->ignored_directories);
    if(isset($uppmconf->build->ignored_files))
        $build->setIgnoredFiles($uppmconf->build->ignored_files);

    $build->build();
    return "";
}, "Build to a .phar file");

$CLI->register("archive", function () {
    global $uppmconf;
    $archive = new Archive();
    $destination = (isset($uppmconf->name)?$uppmconf->name:"project")."-".(isset($uppmconf->version)?$uppmconf->version:"1.0").".zip";
    $source = ".";
    $ignore = [];

    if(isset($uppmconf->archive->ignore))
        $ignore = $uppmconf->archive->ignore;
    if(isset($uppmconf->archive->src))
        $source = $uppmconf->archive->src;
    if(isset($uppmconf->archive->output))
        $destination = $uppmconf->archives->output;

    $archive->setIgnore($ignore);
    $archive->build($source, $destination);
}, "Archives the project!");

$CLI->register("deploy", function () {

}, "Deploy:
        SOON
");

$CLI->register("info", function(){
    global $uppmconf;
    echo Colors::GREEN."---== ".Colors::TURQUIOUS."UPPM".Colors::GREEN." ==---".Colors::ENDC."\n";
    echo "Overview: \n";

    if ($uppmconf->name != null)
        echo Colors::OKBLUE."Name: ".Colors::YELLOW.$uppmconf->name.Colors::ENDC."\n";
    if ($uppmconf->version!= null)
        echo Colors::OKBLUE."Version: ".Colors::YELLOW.$uppmconf->version.Colors::ENDC."\n";
    if ($uppmconf->author!= null)
        echo Colors::OKBLUE."Author: ".Colors::YELLOW.$uppmconf->author.Colors::ENDC."\n";

    echo "\nDependencies: \n";
    foreach ($uppmconf->modules as $module=>$ver)
        echo "- ".Colors::OKBLUE.$module." ".Colors::YELLOW.$ver.Colors::ENDC."\n";

    echo Colors::GREEN."---== ".Colors::TURQUIOUS."-==-".Colors::GREEN." ==---".Colors::ENDC."\n";
});

$CLI->register("lock", function(){
    if (file_exists(UPPM_CURRENT_DIRECTORY."uppm.locks.json"))
        unlink(UPPM_CURRENT_DIRECTORY."uppm.locks.json");
    foreach (scandir(UPPM_CURRENT_DIRECTORY."modules") as $folder){
        if ($folder != '.' && $folder != '..') {
            if(file_exists(UPPM_CURRENT_DIRECTORY."modules/".$folder."/uppm.json")) {
                Colors::info("Writing $folder information (Namespaces, cli-scripts...) into locks.");
                Tools::lockFile(json_decode(file_get_contents(UPPM_CURRENT_DIRECTORY."modules/".$folder."/uppm.json")));
            }
        }
    }
});

if (isset($argv[1]))
    $CLI->run($argv[1], $argv);
else
    $CLI->run("", $argv);
$lockFile = Configs::getLockFile();
if (isset($lockFile->packages->{"TEMPNULL-------"})) {
    unset($lockFile->packages->{"TEMPNULL-------"});
    file_put_contents(UPPM_CURRENT_DIRECTORY."uppm.locks.json", json_encode($lockFile, JSON_PRETTY_PRINT));
}
?>