<?php
(include 'autoload.php')(mod: "main");

use de\interaapps\jsonplus\JSONModel;

class Test {
    use JSONModel;
    public string|null $hello = null;
}

$t = Test::fromJson("{}");
echo $t->hello;
echo $t->toJson();