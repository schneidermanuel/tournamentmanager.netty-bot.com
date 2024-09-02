<?php

namespace Manuel\Tournamentmanager\controllers;

use Manuel\Tournamentmanager\Core\Request;
use Schneidermanuel\Dynalinker\Controller\HttpGet;
use Schneidermanuel\Dynalinker\Core\Dynalinker;
use Manuel\Tournamentmanager\Entities\UserConfigurationEntity;
use UserConfigurationEntity as UserConfigurationEntityUserConfigurationEntity;

class AuthController
{
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
        if (!isset($header)) {
            Request::CloseWithError("Unauthorized", 401);
        }

        $token = explode(" ", $header)[1];
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
        $avatarUrl = $this->GetAvatarUrl($result);

        $userConfigurationStore = Dynalinker::Get()->CreateStore(UserConfigurationEntity::class);
        $userConfigurationFilter = new UserConfigurationEntity();
        $userConfigurationFilter->UserId = $userId;
        $userConfigurationFilter->CanManage = 1;
        $userConfigurations = $userConfigurationStore->LoadWithFilter($userConfigurationFilter);
        $manage = true;
        if (count($userConfigurations) == 0) {
            $manage = false;
        }

        $result = new \stdClass();
        $result->UserId = $userId;
        $result->DisplayName = $displayName;
        $result->AvatarUrl = $avatarUrl;
        $result->Manage = $manage;
        Request::CloseWithMessage($result, "AuthInfo");
    }

    private function GetAvatarUrl($response): string
    {

        if (!isset($response->avatar)) {
            return "https://external-preview.redd.it/4PE-nlL_PdMD5PrFNLnjurHQ1QKPnCvg368LTDnfM-M.png?auto=webp&s=ff4c3fbc1cce1a1856cff36b5d2a40a6d02cc1c3";
        }
        $avatar_url = "https://cdn.discordapp.com/avatars/" . $response->id . "/" . $response->avatar . ".png?size=4096";
        return $avatar_url;
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
