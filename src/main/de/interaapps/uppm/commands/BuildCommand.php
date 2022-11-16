<?php

namespace de\interaapps\uppm\commands;


use BadMethodCallException;
use Phar;

class BuildCommand extends Command {
    public function execute(array $args) {
        $config = $this->uppm->getCurrentProject()->getConfig();
        $outputLocation = $this->uppm->getCurrentDir() . "/" . ($config?->build?->outputDir ?? "target");
        $outputFile = $config?->build?->outputName ?? "{name}-{version}";

        $ignored = $config?->build?->ignored ?? [];

        $run = $config->run->{$config?->build?->run ?? "start"};

        $outputFile = str_replace("{name}", $config->name, str_replace("{version}", $config->version, $outputFile)) . ".phar";

        $this->uppm->getLogger()->loadingBar(0, "Creating phar...");

        $this->uppm->getLogger()->loadingBar(0.2, "Creating folders...");
        if (!file_exists($outputLocation))
            mkdir($outputLocation);

        $this->uppm->getLogger()->loadingBar(0.4, "Removing same named output files...");

        if (file_exists($outputLocation . "/" . $outputFile))
            unlink($outputLocation . "/" . $outputFile);
        if (file_exists($outputLocation . "/" . $outputFile . ".gz"))
            unlink($outputLocation . "/" . $outputFile . ".gz");

        $this->uppm->getLogger()->loadingBar(0.5, "Output File: $outputLocation/$outputFile");
        $phar = new Phar($outputLocation . "/" . $outputFile);
        $st = $phar->createDefaultStub($run);

        $phar->buildFromDirectory(".", '/^(?!(.*target))' . (function () use ($ignored) {
                $out = "";
                foreach ($ignored as $directory)
                    $out .= '(?!(.*' . preg_quote($directory) . '))';
                return $out;
            })() . '(.*)$/i');

        $this->uppm->getLogger()->loadingBar(0.6, "Setting stub...");

        //$phar->setDefaultStub($run, "/" . $run);
        $phar->setStub("#!/usr/bin/env php \n" . $st);

        if (file_exists($outputLocation . "/" . $outputFile . ".gz")) {
            unlink($outputLocation . "/" . $outputFile . ".gz");
        }

        $this->uppm->getLogger()->loadingBar(0.8, "Compressing phar...");
        $phar->compress(Phar::GZ);

        // $this->uppm->getLogger()->info("Creating executable..."); $phar->convertToExecutable(Phar::TAR, Phar::GZ, '.phar.tgz');

        foreach ($ignored as $file) {
            try {
                if ($phar->hasChildren($file))
                    $phar->delete($file);
            } catch (BadMethodCallException) {
            }

        }
        $this->uppm->getLogger()->loadingBar(1, "Done");

        $this->uppm->getLogger()->log("");
        $this->uppm->getLogger()->info("Done! Created $outputLocation/$outputFile.");
    }
}