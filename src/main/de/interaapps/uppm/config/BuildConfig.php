<?php

namespace de\interaapps\uppm\config;


use de\interaapps\jsonplus\JSONModel;

class BuildConfig {
    use JSONModel;

    public string $type;
    public string $run;
    public string $outputName;
    public string $outputDir;
}