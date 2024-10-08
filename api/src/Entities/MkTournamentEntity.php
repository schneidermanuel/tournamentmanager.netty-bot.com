<?php

namespace Manuel\Tournamentmanager\Entities;

use Schneidermanuel\Dynalinker\Entity\Attribute\Entity;
use Schneidermanuel\Dynalinker\Entity\Attribute\Persist;
use Schneidermanuel\Dynalinker\Entity\Attribute\PrimaryKey;

#[Entity("mk_tournament")]
class MkTournamentEntity
{
    #[PrimaryKey()]
    #[Persist("tournamentId")]
    public $TournamentId;
    #[Persist("name")]
    public $Name;
    #[Persist("organisatorDcId")]
    public $OrganisatorDcId;
    #[Persist("organiserDisplayName")]
    public $OrganiserDisplayName;
    #[Persist("status")]
    public $Status;
    #[Persist("guildId")]
    public $GuildId;
    #[Persist("created")]
    public $CreatedDate;
    #[Persist("identifier")]
    public $Code;
    public $Users;
    public $GuildName;
    public $CanManage;
    public $DetailsLoaded;

}
