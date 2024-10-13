<?php

namespace Manuel\Tournamentmanager\controllers;

use Manuel\Tournamentmanager\Core\DiscordApi;
use Manuel\Tournamentmanager\Core\HeaderHelper;
use Manuel\Tournamentmanager\Core\Request;
use Schneidermanuel\Dynalinker\Controller\HttpGet;

class DiscordController
{
    private DiscordApi $api;

    public function __construct()
    {
        $this->api = new DiscordApi();
    }


    #[HttpGet("discordServers")]
    public function GetDiscordServers()
    {
        $header = HeaderHelper::getHeader("Authorization");
        if (!isset($header)) {
            Request::CloseWithError("Unauthorized", 401);
        }
        $token = explode(" ", $header)[1];
        $results = $this->api->GetUserServers($token);
        Request::CloseWithMessage($results, "SERVERS");
    }

    #[HttpGet("roles/.*")]
    public function GetDiscordRoles($guildId)
    {
        $header = HeaderHelper::getHeader("Authorization");
        if (!isset($header)) {
            Request::CloseWithError("Unauthorized", 401);
        }
        $token = explode(" ", $header)[1];
        $roles = $this->api->GetRolesByGuildId($guildId);
        Request::CloseWithMessage($roles, "ROLES");
    }
}
