<?php
/**
 * - ULOLEPHPPACKAGEMANAGER -
 * 
 * Colors
 * Using colors in the CLI!
 * 
 * @author InteraApps
 */

 class Colors {
     public const HEADER = "\033[95m",
                  OKBLUE = "\033[94m",
                  OKGREEN = "\033[92m",
                  WARNING = "\033[93m",
                  FAIL = "\033[91m",
                  ENDC = "\033[0m",
                  BOLD = "\033[1m",
                  UNDERLINE = "\033[4m",
                  RED = "\033[31m",
                  BLUE = "\033[34m",
                  YELLOW = "\033[33m",
                  TURQUIOUS = "\033[36m",
                  GREEN = "\033[32m",
                  BLINK = "\033[5m",
                  BG_RED = "\033[41m",
                  BG_BLUE = "\033[44m",
                  BG_GREEN = "\033[42m",
                  BG_YELLOW = "\033[43m",
                  BG_BLACK = "\033[40m";
        
    public const PREFIX_DONE = "\033[92m᮰ Done\033[0m: ",
                 PREFIX_WARN = "\033[93m᮰ WARNING\033[0m: ",
                 PREFIX_INFO = "\033[36m᮰ INFO\033[0m: ",
                 PREFIX_ERROR = "\033[91m᮰ ERROR\033[0m: ";

    public static function info($str){
        echo self::PREFIX_INFO.$str.self::ENDC."\n";
    }

     public static function warning($str){
         echo self::PREFIX_WARN.$str.self::ENDC."\n";
     }

     public static function done($str){
         echo self::PREFIX_DONE.$str.self::ENDC."\n";
     }

     public static function error($str){
         echo self::PREFIX_ERROR.$str.self::ENDC."\n";
     }
 }

 ?>