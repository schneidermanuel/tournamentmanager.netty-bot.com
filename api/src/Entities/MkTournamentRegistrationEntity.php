<?php

namespace Manuel\Tournamentmanager\Entities;

use Schneidermanuel\Dynalinker\Entity\Attribute\Entity;
use Schneidermanuel\Dynalinker\Entity\Attribute\Persist;
use Schneidermanuel\Dynalinker\Entity\Attribute\PrimaryKey;

#[Entity("mk_tournamentRegistration")]
class MkTournamentRegistrationEntity
{
    #[PrimaryKey]
    #[Persist("tournamentRegistrationId")]
    public $Id;
    #[Persist("tournamentId")]
    public $TournamentId;
    #[Persist("discordId")]
    public $DiscordId;
    #[Persist("registrationTimestamp")]
    public $Timestamp;
    #[Persist("playerName")]
    public $PlayerName;
    #[Persist("friendcode")]
    public $Firendcode;
    #[Persist("host")]
    public $CanHost;
}