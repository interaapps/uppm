#!/usr/bin/env php
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

chdir(".");
(include 'autoload.php')(mod: "main"); // Using the autoloader with the module main
array_shift($argv); // Removing useless first argument
de\interaapps\uppm\UPPM::main($argv);