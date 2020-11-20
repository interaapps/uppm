<?php
/**
 *
 */
$output = '';

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