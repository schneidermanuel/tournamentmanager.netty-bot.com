<?php

namespace Manuel\Tournamentmanager\Core;

class Request
{
    public static function CloseWithError($data, int $statusCode = 500)
    {
        $resultObj = new \stdClass();
        $resultObj->Message = $data;
        $resultObj->Type = "ERROR";
        http_response_code($statusCode);
        echo json_encode($resultObj);
        die();
    }

    public static function CloseWithMessage($data, string $type)
    {
        $resultObj = new \stdClass();
        $resultObj->Message = $data;
        $resultObj->Type = $type;
        http_response_code(200);
        echo json_encode($resultObj);
        die();
    }
}