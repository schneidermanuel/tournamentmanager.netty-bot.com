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
use stdClass;

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
        $tournamentEntity->DetailsLoaded = true;
        Request::CloseWithMessage($tournamentEntity, "TOURNAMENT");
    }

    #[HttpPost(".*/updatePlayer")]
    public function UpdatePlayer($tournamentIdentifier)
    {
        $user = $this->CloseIfUserIsNotAuthorized();
        $tournamentFilter = new MkTournamentEntity();
        $tournamentFilter->OrganisatorDcId = $user->UserId;
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