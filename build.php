<?php

$output = '';

$files = [
    "start.php",

    "Tools.php",
    "Init.php",
    "Install.php",

    "cli/Colors.php",
    "cli/CLI.php",
    "cli/Registers.php"
];

foreach ($files as $file) {
    $output .= file_get_contents("src/".$file)."\n";
}

file_put_contents("uppm", $output);