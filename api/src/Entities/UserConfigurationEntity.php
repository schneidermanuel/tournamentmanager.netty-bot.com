<?php

namespace Manuel\Tournamentmanager\Entities;

use Schneidermanuel\Dynalinker\Entity\Attribute\Entity;
use Schneidermanuel\Dynalinker\Entity\Attribute\Persist;
use Schneidermanuel\Dynalinker\Entity\Attribute\PrimaryKey;

#[Entity("userConfiguration")]
class UserConfigurationEntity
{
    #[PrimaryKey()]
    #[Persist("configId")]
    public $Id;

    #[Persist("userId")]
    public $UserId;

    #[Persist("mkTournamentHost")]
    public $CanManage;

}
