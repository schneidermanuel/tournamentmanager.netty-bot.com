<?php

namespace Manuel\Tournamentmanager\controllers;

use Schneidermanuel\Dynalinker\Controller\HttpGet;

class AuthController
{
    #[HttpGet("Authenticated")]
    public function Authenticated(): void
    {
        $code = $_GET["code"];
        $this->processCode($code);
    }

    public function processCode(string $code): string
    {
        $url = 'https://discord.com/api/v10/oauth2/token';
        $data = [
            "grant_type" => "authorization_code",
            "code" => $code,
            "redirect_uri" => "https://" . $_SERVER["SERVER_NAME"] . "/Login/Authenticated",
            "client_id" => $_ENV["DCCLIENTID"],
            "client_secret" => $_ENV["DCCLIENTSECRET"]
        ];
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];


        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        preg_match('/([0-9])\d+/', $http_response_header[0], $matches);
        $responsecode = intval($matches[0]);
        if ($responsecode != 200) {
            $this->returnResult();
            die();
        }

        $result = json_decode($result);
        $token = $result->access_token;
        $url = 'https://discord.com/api/v10/oauth2/@me';
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Bearer $token",
            ],
        ];


        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $result = json_decode($result);
        $displayName = $result->user->global_name;
        $userId = $result->user->id;
        $this->returnResult($displayName);
    }

    public function returnResult(string $jwt = null): void
    {
        if (isset($jwt)) {
            header("Location: " . $_ENV["FRONTEND_URL"] . "/authenticated/" . $jwt);
        } else {
            header("Location: " . $_ENV["FRONTEND_URL"] . "/error");
        }
    }
}
