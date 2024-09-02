<?php

namespace Manuel\Tournamentmanager\Core;
class HeaderHelper
{
    public static function getHeader($key)
    {
        $headers = apache_request_headers();
        $header = $headers[$key];
        return $header;
    }
}
