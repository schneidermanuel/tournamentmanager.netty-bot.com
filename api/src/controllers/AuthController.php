<?php

namespace Manuel\Tournamentmanager\controllers;

use Manuel\Tournamentmanager\Core\DiscordApi;
use Manuel\Tournamentmanager\Core\HeaderHelper;
use Manuel\Tournamentmanager\Core\Request;
use Manuel\Tournamentmanager\Entities\UserConfigurationEntity;
use Schneidermanuel\Dynalinker\Controller\HttpGet;
use Schneidermanuel\Dynalinker\Core\Dynalinker;

class AuthController
{
    private DiscordApi $api;

    function __construct()
    {
        $this->api = new DiscordApi();
    }

    #[HttpGet("Authenticated")]
    public function Authenticated(): void
    {
        $code = $_GET["code"];
        $this->processCode($code);
    }

    public function processCode(string $code): void
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


        $this->returnResult($token);
    }

    #[HttpGet("me")]
    public function GetUser()
    {
        $header = HeaderHelper::getHeader("Authorization");
        if (strlen($header) == 0) {
            Request::CloseWithError("Unauthorized", 401);
        }
        $token = explode(" ", $header)[1];
        $user = $this->api->GetUserInfoFromToken($token);

        $userConfigurationStore = Dynalinker::Get()->CreateStore(UserConfigurationEntity::class);
        $userConfigurationFilter = new UserConfigurationEntity();
        $userConfigurationFilter->UserId = $user->UserId;
        $userConfigurationFilter->CanManage = 1;
        $userConfigurations = $userConfigurationStore->LoadWithFilter($userConfigurationFilter);
        $manage = true;
        if (count($userConfigurations) == 0) {
            $manage = false;
        }

        $result = new \stdClass();
        $result->UserId = $user->UserId;
        $result->DisplayName = $user->DisplayName;
        $result->AvatarUrl = $user->AvatarUrl;
        $result->Manage = $manage;
        Request::CloseWithMessage($result, "AuthInfo");
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
