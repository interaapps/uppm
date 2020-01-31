<?php
/**
 * - ULOLEPHPPACKAGEMANAGER -
 * 
 * Tools
 * 
 * @author InteraApps
 */

 class Tools {

    public static function getStringBetween($string, $start, $end){
        $string = $string;
        $ini = @strpos($string, $start);
        $ini += strlen($start);
        if ($end=="") {
          return substr($string, $ini, strlen($string));
        }
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string , ($start == "") ? 0 : $ini , $len);
    }

    public static function deleteDir($dirPath, $ignore="NON++++++++++++NON+++++++++++++++NON") {
        if (!is_dir($dirPath))
            return;
        
        $files = scandir($dirPath);
        foreach ($files as $file) 
            if ($file != "." && $file != ".." && $file != $ignore) {
                if (is_dir($dirPath."/".$file)) {
                    self::deleteDir($dirPath."/".$file);
                } else {
                    if (!($dirPath=="./" && $file=="uppm"))
                        unlink($dirPath."/".$file);
                }
            }
            
        rmdir($dirPath);
    }

     public static function statusIndicator($done, $total, $size=30) {

         static $start_time;

         if($done > $total) return;

         if(empty($start_time)) $start_time=time();

         $perc=(double)($done/$total);

         $bar=floor($perc*$size);

         $status_bar="\r┋";
         $status_bar.=str_repeat(Colors::GREEN."▉".Colors::ENDC, $bar);
         if($bar<$size){
             $status_bar.=Colors::YELLOW."░".Colors::ENDC;
             $status_bar.=str_repeat(Colors::YELLOW."░".Colors::ENDC, $size-$bar);
         } else {
             $status_bar .= Colors::GREEN."▉".Colors::ENDC;
         }

         $status_bar.="┋ ".number_format($perc*100, 0)." %  $done/$total";


         echo "$status_bar  ";

         flush();

     }


     function copyDir($src, $dst) {
         $dir = opendir($src);
         @mkdir($dst);

         while( $file = readdir($dir) ) {
             if (( $file != '.' ) && ( $file != '..' )) {
                 if ( is_dir($src . '/' . $file) ) {
                     self::copyDir($src . '/' . $file, $dst . '/' . $file);
                 }
                 else {
                     copy($src . '/' . $file, $dst . '/' . $file);
                 }
             }
         }
         closedir($dir);
     }

     public static function downloadAutoloader(){
         if (file_exists("autoload.php")) {
             Colors::info("The file: 'autoload.php' already exists. Do you want to override it? [YES,No]");
             if (!(readline() == "" || strtoupper(readline()) == "YES"))
                return;

         }


         file_put_contents("autoload.php", file_get_contents("https://raw.githubusercontent.com/interaapps/uppm/master/autoload.php"));
     }

 }

 ?>