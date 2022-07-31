<?php

namespace de\interaapps\uppm\package\uppm\models;

use de\interaapps\jsonplus\JSONModel;

class PackageResponse {
    use JSONModel;

    public int $id;
    public int $organisation_id;
    public string $name;
    public string $github;
    public string $created_at;
}