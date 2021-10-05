<?php
namespace de\interaapps\uppm\package\uppm\models;

use de\interaapps\jsonplus\JSONModel;

class PackageVersionResponse {
    use JSONModel;

    public bool $error = false;

    public int $id;
    public int $package_id;
    public string $name;
    public string $download_url;
    public string $created_at;
}