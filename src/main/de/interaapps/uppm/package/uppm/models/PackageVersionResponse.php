<?php
namespace de\interaapps\uppm\package\uppm\models;

use de\interaapps\jsonplus\JSONModel;

class PackageVersionResponse {
    use JSONModel;

    public bool $error = false;

    public int $id;
    public int $package_id;
    public string $name;
    public string|null $download_url = null;
    public string $created_at;
}