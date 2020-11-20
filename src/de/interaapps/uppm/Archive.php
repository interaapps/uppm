<?php
/**
 * - ULOLEPHPPACKAGEMANAGER -
 *
 * Tools
 *
 * @author InteraApps
 */
namespace de\interaapps\uppm;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use de\interaapps\uppm\cli\Colors;

class Archive {

    private $ignore = [];

    public function build($source, $destination) : void {
        if (!file_exists(UPPM_CURRENT_DIRECTORY."uppm_target"))
            mkdir(UPPM_CURRENT_DIRECTORY."uppm_target");
        if (!file_exists(UPPM_CURRENT_DIRECTORY."uppm_target/archives"))
            mkdir(UPPM_CURRENT_DIRECTORY."uppm_target/archives");

        $source = UPPM_CURRENT_DIRECTORY.$source;

        $destination = UPPM_CURRENT_DIRECTORY."uppm_target/archives/".$destination;
        if (file_exists(UPPM_CURRENT_DIRECTORY.$destination))
            unlink(UPPM_CURRENT_DIRECTORY.$destination);

        if (!extension_loaded('zip') || !file_exists($source)) {
            Colors::error("Zip not found");
            return;
        }

        $zip = new \ZipArchive();
        if (!$zip->open($destination, \ZIPARCHIVE::CREATE)) {
            Colors::error("Error while opening $destination");
            return;
        }

        $source = str_replace('\\', '/', realpath($source));

        $ignore = [];

        foreach ($this->ignore as $ignoreMe) {
            Colors::info("Ignoring ".UPPM_CURRENT_DIRECTORY."".$ignoreMe);
            array_push($ignore, UPPM_CURRENT_DIRECTORY."" . $ignoreMe);
        }


        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            $filesDone = 0;
            $filesCount = 0;
            foreach ($files as $file)
                $filesCount++;

            foreach ($files as $file) {
                $file = UPPM_CURRENT_DIRECTORY.$file;
                $filesDone++;
                Tools::statusIndicator($filesDone, $filesCount, 50);

                $file = str_replace('\\', '/', $file);

                if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                    continue;

                $file = realpath($file);

                if (!in_array($file, $ignore)) {
                    if (is_dir($file) === true)
                        $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                    else if (is_file($file) === true)
                        $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents(UPPM_CURRENT_DIRECTORY.$file));
                }
            }
            echo "\n";
            Colors::done("Done! Zipped to $destination");
        } else if (is_file($source) === true)
            $zip->addFromString(basename($source), file_get_contents($source));

        $zip->close();
    }

    private function ignoreRecursive($dir) {
        foreach (scandir($dir) as $item) {
            if ($item != "." && $item != ".."){
                if (is_dir($dir . "/" . $item)) {
                    array_push($this->ignore, $dir."/".$item);
                    Colors::info("Ignoring directory $dir/$item");
                    $this->ignoreRecursive($dir . "/" . $item);
                } else
                    array_push($this->ignore, $dir."/".$item);
            }
        }
    }

    public function setIgnore(array $ignore): void{
        $this->ignore = $ignore;
        foreach ($ignore as $item)
            if (is_dir($item)) $this->ignoreRecursive($item);
        if (is_dir("uppm_target")) {
            array_push($this->ignore, "uppm_target");
            $this->ignoreRecursive("uppm_target");
        }
    }

}

?>