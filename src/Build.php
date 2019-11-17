<?php
/**
 * - ULOLEPHPPACKAGEMANAGER -
 * 
 * INIT
 * 
 * @author InteraApps
 */

class Build {

    private $directory;
    private $outputLocation = "uppm_target";
    private $main = false;
    private $outputFile = "test.phar";
    private $ignoredDirectories = [];
    private $ignoredFiles = [];

    public function __construct(){

    }

    public function build() : void {

        Colors::info("UPPM Build - Building into a phar");

        Colors::info("Building...");

        if (file_exists(getcwd()."/".$this->outputLocation."/".$this->outputFile))
            unlink(getcwd()."/".$this->outputLocation."/".$this->outputFile);
        if (file_exists(getcwd()."/".$this->outputLocation."/".$this->outputFile.".gz"))
            unlink(getcwd()."/".$this->outputLocation."/".$this->outputFile.".gz");

        if (!file_exists($this->outputLocation)) {
            Colors::info("Creating dir: ".$this->outputLocation);
            mkdir($this->outputLocation);
        }

        Colors::info("Initializing PHP-Phar");
        $phar = new Phar(getcwd()."/".$this->outputLocation."/".$this->outputFile);

        Colors::info("BuildFromDirectory: ".$this->directory);
        $phar->buildFromDirectory(getcwd()."/".$this->directory, '/^(?!(.*uppm_target))'.(function(){
            $out = "";
            foreach ($this->ignoredDirectories as $directory)
                $out .= '(?!(.*'.str_replace("/","\\/",$directory).'))';
            return $out;
        })().'(.*)$/i');


        if ($this->main !== false) {
            Colors::info("Setting stub to".$this->main);
            $phar->setDefaultStub($this->main, "/" . $this->main);
        } else
            Colors::warning("main haven't been found in uppm.json (\"build\": {\"main\": NULL (NOT FOUND)})");


        if (file_exists(getcwd()."/".$this->outputLocation."/".$this->outputFile.".gz")) {
            Colors::info("Removing old ".$this->outputFile.".gz");
            unlink(getcwd() . "/" . $this->outputLocation . "/" . $this->outputFile . ".gz");
        }

        Colors::info("Compressing...");
        $phar->compress(Phar::GZ);
        Colors::done("Built into the file ".getcwd()."/".$this->outputLocation."/".$this->outputFile);

        foreach ($this->ignoredFiles as $file) {
            try {
                Colors::info("Removing file in Phar: ".$file);
                $phar->delete($file);
            } catch (BadMethodCallException $e) {}
        }
        Colors::done("Done!");

    }

    public function setDirectory(string $directory) : void{
        $this->directory = $directory;
    }

    public function setOutputFile(string $file) : void{
        $this->outputFile = $file;
    }

    public function setMain(string $main) : void{
        $this->main = $main;
    }

    public function setIgnoredFiles(array $ignoredFiles) : void {
        $this->ignoredFiles = $ignoredFiles;
    }

    public function setIgnoredDirectories(array $ignoredDirectories): void{
        $this->ignoredDirectories = $ignoredDirectories;
    }

    public function setOutputLocation(string $outputLocation): void{
        $this->outputLocation = $outputLocation;
    }

}

?>