<?php

namespace Manuel\Tournamentmanager\controllers;

use Manuel\Tournamentmanager\Core\DiscordApi;
use Manuel\Tournamentmanager\Core\DiscordUser;
use Manuel\Tournamentmanager\Core\HeaderHelper;
use Manuel\Tournamentmanager\Core\Request;
use Manuel\Tournamentmanager\Entities\MkTournamentEntity;
use Manuel\Tournamentmanager\Entities\MkTournamentRegistrationEntity;
use Manuel\Tournamentmanager\Entities\UserConfigurationEntity;
use Schneidermanuel\Dynalinker\Controller\HttpGet;
use Schneidermanuel\Dynalinker\Controller\HttpPost;
use Schneidermanuel\Dynalinker\Core\Dynalinker;
use Schneidermanuel\Dynalinker\Entity\EntityStore;

class TournamentsController
{
    private DiscordApi $api;
    private EntityStore $userStore;
    private EntityStore $tournamentStore;
    private EntityStore $tournamentRegistrationStore;

    function __construct()
    {
        $dynalinker = Dynalinker::Get();
        $this->api = new DiscordApi();
        $this->userStore = $dynalinker->CreateStore(UserConfigurationEntity::class);
        $this->tournamentStore = $dynalinker->CreateStore(MkTournamentEntity::class);
        $this->tournamentRegistrationStore = $dynalinker->CreateStore(MkTournamentRegistrationEntity::class);
    }

    #[HttpGet("list")]
    public function List()
    {
        $user = $this->CloseIfUserIsNotAuthorized();
        $myActiveTouneyFilter = new MkTournamentEntity();
        $myActiveTouneyFilter->OrganisatorDcId = $user->UserId;
        $myActiveTouneyFilter->Status = 'preparation';
        $myTourneysPrep = $this->tournamentStore->LoadWithFilter($myActiveTouneyFilter);
        $myActiveTouneyFilter->Status = 'join';

        $myTourneysJoin = $this->tournamentStore->LoadWithFilter($myActiveTouneyFilter);
        $myOpenTourneys = array_merge($myTourneysPrep, $myTourneysJoin);
        $closedTourneys = $this->tournamentStore->CustomQuery("SELECT * FROM mk_tournament WHERE organisatorDcId = '$user->UserId' AND status IN ('closed', 'completed') ORDER BY created DESC LIMIT 5");
        $otherTourneys = $this->tournamentStore->CustomQuery("SELECT * FROM mk_tournament WHERE organisatorDcId != '$user->UserId' ORDER BY created DESC LIMIT 10");

        $result = new \stdClass();
        $result->OpenTournaments = $myOpenTourneys;
        $result->ClosedTournaments = $closedTourneys;
        $result->OtherTournaments = $otherTourneys;
        Request::CloseWithMessage($result, "TOURNAMENTS");
    }

    #[HttpPost("create")]
    public function CreateTournament()
    {
        $user = $this->CloseIfUserIsNotAuthorized();

        if (strlen($_POST['Name']) < 5) {
            Request::CloseWithError("Tournament name must be at least 5 characters long", 400);
        }

        $tournament = new MkTournamentEntity();
        $tournament->Name = $_POST['Name'];
        $tournament->OrganisatorDcId = $user->UserId;
        $tournament->OrganiserDisplayName = $user->DisplayName;
        $tournament->Status = 'preparation';
        $tournament->GuildId = $_POST['GuildId'];
        $tournament->CreatedDate = date("Y-m-d H:i:s");
        if (isset($_POST["RoleId"]))
        {
            $tournament->RoleId = $_POST["RoleId"];
        }
        $tournament->Code = substr(bin2hex(random_bytes(16)), 0, 4);

        $token = HeaderHelper::getHeader("Authorization");
        if (!isset($token)) {
            Request::CloseWithError("Unauthorized", 401);
        }
        $token = explode(" ", $token)[1];

        $guilds = $this->api->GetUserServers($token);
        $matchingGuild = null;

        foreach ($guilds as $guild) {
            if ($guild->ServerId == $_POST['GuildId']) {
                $matchingGuild = $guild;
                break;
            }
        }

        if ($matchingGuild === null) {
            Request::CloseWithError("Guild not found", 404);
        }

        $tournament->GuildName = $matchingGuild->ServerName;

        $this->tournamentStore->SaveOrUpdate($tournament);
        Request::CloseWithMessage("Tournament created", "OK");
    }


    #[HttpGet("detail/.*")]
    public function GetDetails($identifier)
    {
        $header = HeaderHelper::getHeader("Authorization");
        if (!isset($header)) {
            Request::CloseWithError("Unauthorized", 401);
        }
        $token = explode(" ", $header)[1];
        $dcUser = $this->CloseIfUserIsNotAuthorized();
        $filter = new MkTournamentEntity();
        $filter->Code = $identifier;
        $res = $this->tournamentStore->LoadWithFilter($filter);
        if (count($res) != 1) {
            Request::CloseWithError("Tournament not found", 404);
        }

        $tournamentEntity = $res[0];
        $usersFilter = new MkTournamentRegistrationEntity();
        $usersFilter->TournamentId = $tournamentEntity->TournamentId;
        $users = $this->tournamentRegistrationStore->LoadWithFilter($usersFilter);
        $tournamentEntity->Users = $users;
        $tournamentEntity->GuildName = $this->api->GetGuildNameById($tournamentEntity->GuildId, $token);
        $tournamentEntity->CanManage = false;
        if ($tournamentEntity->OrganisatorDcId == $dcUser->UserId) {
            $tournamentEntity->CanManage = true;
        }
        if (isset($tournamentEntity->RoleId))
        {
            $roles = $this->api->GetRolesByGuildId($tournamentEntity->GuildId);
            $role = array_reduce($roles, function($carry, $role) use ($tournamentEntity) {
                return $role->RoleId == $tournamentEntity->RoleId ? $role : $carry;
            });
            $tournamentEntity->RoleName = $role->RoleName;

        }
        $tournamentEntity->DetailsLoaded = true;
        Request::CloseWithMessage($tournamentEntity, "TOURNAMENT");
    }

    #[HttpPost(".*/updatePlayer")]
    public function UpdatePlayer($tournamentIdentifier)
    {
        $user = $this->CloseIfUserIsNotAuthorized();
        $tournamentFilter = new MkTournamentEntity();
        $tournamentFilter->OrganisatorDcId = $user->UserId;
        $tournamentFilter->Code = $tournamentIdentifier;
        $tournaments = $this->tournamentStore->LoadWithFilter($tournamentFilter);
        if (count($tournaments) != 1) {
            Request::CloseWithError("Tournament not found", 404);
        }
        $tournament = $tournaments[0];
        $playerEntityFilter = new MkTournamentRegistrationEntity();
        $playerEntityFilter->TournamentId = $tournament->TournamentId;
        $playerEntityFilter->DiscordId = $_POST["DiscordId"];
        $players = $this->tournamentRegistrationStore->LoadWithFilter($playerEntityFilter);
        if (count($players) != 1) {
            Request::CloseWithError("Bad Request");
        }
        if (!preg_match('/[A-Za-z]{2}-\d{4}-\d{4}-\d{4}/i', $_POST["Friendcode"])) {
            Request::CloseWithError("Bad Friendcode", 400);
        }
        $playerEntity = $players[0];
        $playerEntity->CanHost = $_POST["CanHost"];
        $playerEntity->Friendcode = $_POST["Friendcode"];
        $playerEntity->Timestamp = $_POST["Timestamp"];
        $this->tournamentRegistrationStore->SaveOrUpdate($playerEntity);
        Request::CloseWithMessage("Player updated", "OK");
    }

    #[HttpPost(".*/setStatus")]
    public function SetStatus($tournamentIdentifier)
    {
        $user = $this->CloseIfUserIsNotAuthorized();
        $tournamentFilter = new MkTournamentEntity();
        $tournamentFilter->OrganisatorDcId = $user->UserId;
        $tournamentFilter->Code = $tournamentIdentifier;
        $tournaments = $this->tournamentStore->LoadWithFilter($tournamentFilter);
        if (count($tournaments) != 1) {
            Request::CloseWithError("Tournament not found", 404);
        }
        $tournament = $tournaments[0];
        $newStatus = $_POST["Status"];
        if ($newStatus != "preparation" && $newStatus != "join" && $newStatus != "closed" && $newStatus != "completed") {
            Request::CloseWithError("Bad Status", 400);
        }
        $tournament->Status = $newStatus;
        $this->tournamentStore->SaveOrUpdate($tournament);
        Request::CloseWithMessage("Tournament updated", "OK");
    }

    #[HttpPost(".*/delete")]
    public function DeleteTournament($tournamentIdentifier)
    {
        $user = $this->CloseIfUserIsNotAuthorized();

        $tournamentFilter = new MkTournamentEntity();
        $tournamentFilter->OrganisatorDcId = $user->UserId;
        $tournamentFilter->Code = $tournamentIdentifier;
        $tournaments = $this->tournamentStore->LoadWithFilter($tournamentFilter);

        if (count($tournaments) != 1) {
            Request::CloseWithError("Tournament not found", 404);
        }

        $tournament = $tournaments[0];
        $registrationFilter = new MkTournamentRegistrationEntity();
        $registrationFilter->TournamentId = $tournament->TournamentId;
        $registrations = $this->tournamentRegistrationStore->LoadWithFilter($registrationFilter);

        foreach ($registrations as $registration) {
            $this->tournamentRegistrationStore->DeleteById($registration->Id);
        }

        $this->tournamentStore->DeleteById($tournament->TournamentId);

        Request::CloseWithMessage("Tournament deleted", "OK");
    }

    #[HttpPost(".*/deletePlayer")]
    public function DeletePlayer($tournamentIdentifier)
    {
        $user = $this->CloseIfUserIsNotAuthorized();

        $tournamentFilter = new MkTournamentEntity();
        $tournamentFilter->OrganisatorDcId = $user->UserId;
        $tournamentFilter->Code = $tournamentIdentifier;
        $tournaments = $this->tournamentStore->LoadWithFilter($tournamentFilter);
        if (count($tournaments) != 1) {
            Request::CloseWithError("Tournament not found", 404);
        }

        $tournament = $tournaments[0];
        $playerEntityFilter = new MkTournamentRegistrationEntity();
        $playerEntityFilter->TournamentId = $tournament->TournamentId;
        $playerEntityFilter->DiscordId = $_POST["DiscordId"];
        $players = $this->tournamentRegistrationStore->LoadWithFilter($playerEntityFilter);

        if (count($players) != 1) {
            Request::CloseWithError("Player not found", 404);
        }
        $playerEntity = $players[0];
        $this->tournamentRegistrationStore->DeleteById($playerEntity->Id);

        Request::CloseWithMessage("Player deleted", "OK");
    }

    /**
     * @return void
     */
    private function CloseIfUserIsNotAuthorized(): DiscordUser
    {
        $header = HeaderHelper::getHeader("Authorization");
        if (!isset($header)) {
            Request::CloseWithError("Unauthorized", 401);
        }
        $token = explode(" ", $header)[1];
        $user = $this->api->GetUserInfoFromToken($token);
        $userConfigurationFilter = new UserConfigurationEntity();
        $userConfigurationFilter->UserId = $user->UserId;
        $userConfigurationFilter->CanManage = 1;
        $userConfigurations = $this->userStore->LoadWithFilter($userConfigurationFilter);
        if (count($userConfigurations) == 0) {
            Request::CloseWithError("Unauthorized", 401);
        }
        return $user;
    }
}