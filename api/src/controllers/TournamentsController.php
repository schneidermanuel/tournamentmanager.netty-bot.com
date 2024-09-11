<?php

namespace Manuel\Tournamentmanager\controllers;

use Manuel\Tournamentmanager\Core\DiscordApi;
use Manuel\Tournamentmanager\Core\HeaderHelper;
use Manuel\Tournamentmanager\Core\Request;
use Manuel\Tournamentmanager\Entities\MkTournamentEntity;
use Manuel\Tournamentmanager\Entities\UserConfigurationEntity;
use Schneidermanuel\Dynalinker\Controller\HttpGet;
use Schneidermanuel\Dynalinker\Core\Dynalinker;
use Schneidermanuel\Dynalinker\Entity\EntityStore;

class TournamentsController
{
    private DiscordApi $api;
    private EntityStore $userStore;
    private EntityStore $tournamentStore;

    function __construct()
    {
        $dynalinker = Dynalinker::Get();
        $this->api = new DiscordApi();
        $this->userStore = $dynalinker->CreateStore(UserConfigurationEntity::class);
        $this->tournamentStore = $dynalinker->CreateStore(MkTournamentEntity::class);
    }

    #[HttpGet("list")]
    public function List()
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
        $result->CloseTournaments = $closedTourneys;
        $result->OtherTournaments = $otherTourneys;
        Request::CloseWithMessage($result, "TOURNAMENTS");

    }
}