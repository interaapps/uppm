#!/usr/bin/env php
<?php
error_reporting(E_ERROR | E_PARSE | E_NOTICE);

chdir(".");
(include 'autoload.php')(mod: "main"); // Using the autoloader with the module main
array_shift($argv); // Removing useless first argument
de\interaapps\uppm\UPPM::main($argv);