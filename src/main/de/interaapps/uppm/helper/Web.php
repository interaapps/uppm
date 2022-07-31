<?php

namespace de\interaapps\uppm\helper;


class Web {
    public static function httpRequest($url = "", $method = "GET") {
        return @file_get_contents($url, false, stream_context_create(["http" => ["method" => $method, "header" => "User-Agent: request"]]));
    }
}