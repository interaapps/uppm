#!/usr/bin/env php
<?php
error_reporting(E_ERROR | E_PARSE | E_NOTICE);

chdir(".");
(include 'autoload.php')(mod: "main");
array_shift($argv);
de\interaapps\uppm\UPPM::main($argv);