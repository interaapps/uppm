<?php 

$CLI = new CLI(1);

$CLI->register("-v", function() {
    return UPPMINFO["version"]."\n";
}, "See UPPMs version");

$CLI->register("init", function() {
    return Init::initFromCLI();
}, "Initializing Project");

$CLI->register("init:fast", function() {
    return Init::initProject("uppm project", "1.0", "", "Me", []);
}, "Initializing Project without any information");

$CLI->register("install", function() {
    global $argv;
    return Install::installNew($argv[2]);
}, "Install a new package
    Types:
      - 'github:' Downloads a project from github (Example: 'user/project' or 'user/project:master')
      - 'web:' Downloads a project from web (Example: https://mywebsite.com/test.zip)
      - 'local:' Unzips a local file 'file.zip'");

$CLI->register("update", function() {
    global $uppmconf;
    $lockFile = Configs::getLockFile();
    $lockFile->packages = ["TEMPNULL-------"=>"TEMPNULL-------"];
    file_put_contents("uppm.locks.json", json_encode($lockFile, JSON_PRETTY_PRINT));
    foreach ($uppmconf->modules as $name=>$version) {
        $resource = new Install($name, $version);
        $resource->download();
    }
}, "Updating all");


if (isset($argv[1]))
    $CLI->run($argv[1], $argv);
else
    echo COLORS::PREFIX_ERROR."Command not found\n";
$lockFile = Configs::getLockFile();
if (isset($lockFile->packages->{"TEMPNULL-------"})) {
    unset($lockFile->packages->{"TEMPNULL-------"});
    file_put_contents("uppm.locks.json", json_encode($lockFile, JSON_PRETTY_PRINT));
}
?>