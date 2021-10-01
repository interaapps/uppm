<?php
namespace de\interaapps\uppm\helper;


class Files {

    public static function deleteDir($dirPath) {
        if (!is_dir($dirPath))
            return;

        $files = scandir($dirPath);
        foreach ($files as $file)
            if ($file != "." && $file != "..") {
                if (is_dir($dirPath."/".$file)) {
                    self::deleteDir($dirPath."/".$file);
                } else {
                    if (!($dirPath=="./" && $file=="uppm"))
                        unlink($dirPath."/".$file);
                }
            }

        rmdir($dirPath);
    }

    public static function copyDir($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);

        while($file = readdir($dir) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    self::copyDir($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

}