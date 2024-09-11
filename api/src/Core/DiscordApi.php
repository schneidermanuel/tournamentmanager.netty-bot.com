<?php

namespace Manuel\Tournamentmanager\Core;

class DiscordApi
{
    public function GetUserInfoFromToken($token) : DiscordUser
    {
        $url = 'https://discord.com/api/v10/oauth2/@me';
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\nAuthorization: Bearer $token",
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($this->GetStatusCode($http_response_header)!=200){
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
    private function GetAvatarUrl($response): string
    {

        if (!isset($response->avatar)) {
            return "https://external-preview.redd.it/4PE-nlL_PdMD5PrFNLnjurHQ1QKPnCvg368LTDnfM-M.png?auto=webp&s=ff4c3fbc1cce1a1856cff36b5d2a40a6d02cc1c3";
        }
        $avatar_url = "https://cdn.discordapp.com/avatars/" . $response->id . "/" . $response->avatar . ".png?size=4096";
        return $avatar_url;
    }
    function GetStatusCode($http_response_header) :int {
        if (isset($http_response_header)) {
            $status_line = $http_response_header[0];
            if (preg_match('{HTTP/\S*\s(\d{3})}', $status_line, $match)) {
                return (int)$match[1];  // Return the status code as an integer
            }
        }
        return -1;
    }
}