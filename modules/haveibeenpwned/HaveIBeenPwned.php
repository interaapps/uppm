<?php
namespace modules\haveibeenpwned;

class HaveIBeenPwned {

    public static function passwords($password) {
        $hashedPassword = strtoupper(hash("sha1", $password));
        $requestHash = substr($hashedPassword, 0, 5);
        $response = \file_get_contents("https://api.pwnedpasswords.com/range/".$requestHash);
        foreach (explode("\n", $response) as $match) {
            $thisLine = explode(":", $match);
            if ($requestHash.$thisLine[0] == $hashedPassword)
                return $thisLine[1];
        }
        return 0;
    }

    

}
