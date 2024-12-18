<?php

namespace Manuel\Tournamentmanager\Core;

class DiscordApi
{
    public function GetGuildNameById($guildId)
    {
        $url = 'https://discord.com/api/v10/guilds/' . $guildId;
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Bot " . $_ENV['BOT_TOKEN'],
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($this->GetStatusCode($http_response_header) != 200) {
            return "unknown";
        }
        $result = json_decode($result);
        return $result->name;
    }

    public function GetUserInfoFromToken($token): DiscordUser
    {
        $url = 'https://discord.com/api/v10/oauth2/@me';
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Bearer $token",
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($this->GetStatusCode($http_response_header) != 200) {
            Request::CloseWithError($result, 401);
        }
        $result = json_decode($result);
        $displayName = $result->user->global_name;
        $userId = $result->user->id;
        $avatarUrl = $this->GetAvatarUrl($result);

        $result = new DiscordUser();
        $result->DisplayName = $displayName;
        $result->AvatarUrl = $avatarUrl;
        $result->UserId = $userId;
        return $result;
    }

    public function GetUserServers($token): array /*array<DiscordServer>*/
    {
        $url = 'https://discord.com/api/v10/users/@me/guilds';
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Bearer $token",
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($this->GetStatusCode($http_response_header) != 200) {
            Request::CloseWithError($result, 401);
        }

        $result = json_decode($result);
        $servers = [];

        foreach ($result as $guild) {
            $server = new DiscordServer();
            $server->ServerName = $guild->name;
            $server->ServerId = $guild->id;

            $servers[] = $server;
        }

        return $servers;
    }

    private function GetAvatarUrl($response): string
    {

        if (!isset($response->avatar)) {
            return "https://external-preview.redd.it/4PE-nlL_PdMD5PrFNLnjurHQ1QKPnCvg368LTDnfM-M.png?auto=webp&s=ff4c3fbc1cce1a1856cff36b5d2a40a6d02cc1c3";
        }
        $avatar_url = "https://cdn.discordapp.com/avatars/" . $response->id . "/" . $response->avatar . ".png?size=4096";
        return $avatar_url;
    }

    function GetStatusCode($http_response_header): int
    {
        if (isset($http_response_header)) {
            $status_line = $http_response_header[0];
            if (preg_match('{HTTP/\S*\s(\d{3})}', $status_line, $match)) {
                return (int)$match[1];  // Return the status code as an integer
            }
        }
        return -1;
    }
    public function GetRolesByGuildId($guildId): array /* array<DiscordRole> */
    {
        $url = 'https://discord.com/api/v10/guilds/' . $guildId . '/roles';
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Bot " . $_ENV['BOT_TOKEN'],
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($this->GetStatusCode($http_response_header) != 200) {
            return [];
        }

        $result = json_decode($result);
        $roles = [];

        foreach ($result as $role) {
            if ($role->name == "@everyone")
            {
                continue;
            }
            $discordRole = new DiscordRole();
            $discordRole->RoleId = $role->id;
            $discordRole->RoleName = $role->name;

            $roles[] = $discordRole;
        }

        return $roles;
    }
}