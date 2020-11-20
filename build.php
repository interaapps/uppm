<?php
/**
 *
 */
$output = '#!/usr/bin/env php
<?php namespace de\interaapps\uppm;
define("UPPM_SINGLE_FILE_INSTANCE", 1); ?>';

$files = [
    "Tools.php",
    "Init.php",
    "Configs.php",
    "Install.php",
    "Build.php",
    "Archive.php",
    "Runner.php",

    "cli/Colors.php",
    "cli/CLI.php",
    "app.php"
];

foreach ($files as $file) {
    $output .= file_get_contents("src/de/interaapps/uppm/".$file)."\n";
}

file_put_contents("uppm", $output);