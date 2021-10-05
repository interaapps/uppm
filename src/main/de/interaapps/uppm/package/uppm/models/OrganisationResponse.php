<?php
namespace de\interaapps\uppm\package\uppm\models;

use de\interaapps\jsonplus\JSONModel;

class OrganisationResponse {
    use JSONModel;

    public int $id;
    public string $name;
    public string $created_at;
}