<?php
namespace de\interaapps\uppm\commands;


use BadMethodCallException;
use de\interaapps\uppm\UPPM;
use Phar;

class BuildCommand implements Command {
    private UPPM $uppm;
    public function __construct(UPPM $uppm) {
        $this->uppm = $uppm;
    }

    public function execute(array $args) {
        $config = $this->uppm->getCurrentProject()->getConfig();
        $outputLocation = $this->uppm->getCurrentDir()."/".($config?->build?->outputDir ?: "target");
        $outputFile = $config?->build?->outputName ?: "{name}-{version}";

        $ignored =  $config?->build?->ignored ?: [];

        $run = $config->run->{$config?->build?->run ?: "start"};

        $outputFile = str_replace("{name}", $config->name, str_replace("{version}", $config->version, $outputFile)).".phar";
        
        $this->uppm->getLogger()->info("Creating phar...");

        $this->uppm->getLogger()->info("Creating folders...");
        if (!file_exists($outputLocation))
            mkdir($outputLocation);

        $this->uppm->getLogger()->info("Removing same named output files...");

        if (file_exists($outputLocation."/".$outputFile))
            unlink($outputLocation."/".$outputFile);
        if (file_exists($outputLocation."/".$outputFile.".gz"))
            unlink($outputLocation."/".$outputFile.".gz");

        $this->uppm->getLogger()->info("Output File: ".$outputLocation."/".$outputFile);
        $phar = new Phar($outputLocation."/".$outputFile);
        $st = $phar->createDefaultStub($run);

        $phar->buildFromDirectory(".", '/^(?!(.*target))'.(function() use ($ignored) {
            $out = "";
            foreach ($ignored as $directory)
                $out .= '(?!(.*'.preg_quote($directory).'))';
            return $out;
        })().'(.*)$/i');

        $this->uppm->getLogger()->info("Setting stub...");

        //$phar->setDefaultStub($run, "/" . $run);
        $phar->setStub("#!/usr/bin/php \n".$st);

        if (file_exists($outputLocation."/".$outputFile.".gz")) {
            unlink($outputLocation . "/" . $outputFile . ".gz");
        }

        $this->uppm->getLogger()->info("Compressing phar...");
        $phar->compress(Phar::GZ);

        // $this->uppm->getLogger()->info("Creating executable..."); $phar->convertToExecutable(Phar::TAR, Phar::GZ, '.phar.tgz');

        foreach ($ignored as $file) {
            try {
                if ($phar->hasChildren($file))
                    $phar->delete($file);
            } catch (BadMethodCallException $e) {}
        }
        $this->uppm->getLogger()->info("Done! Created $outputLocation/$outputFile.");
    }
}