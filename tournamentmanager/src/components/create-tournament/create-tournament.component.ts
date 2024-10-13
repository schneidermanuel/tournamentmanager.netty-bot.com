import { Component, EventEmitter, OnInit, Output } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Tournament } from '../../Data/Tournament';
import { NgClass, NgFor, NgIf } from '@angular/common';
import { TournamentService } from '../../Service/TournamentsService';
import { DiscordServer } from '../../Data/DiscordServer';
import { DiscordRole } from '../../Data/DiscordRole';

@Component({
  selector: 'createTournament',
  standalone: true,
  imports: [
    FormsModule,
    NgIf,
    NgFor,
    NgClass
  ],
  templateUrl: './create-tournament.component.html',
  styleUrl: './create-tournament.component.css'
})
export class CreateTournamentComponent implements OnInit {
  public Tournament: Tournament;
  public Servers: DiscordServer[] = [];
  public Roles: DiscordRole[] = [];
  public RolesLoaded: boolean = true;

  @Output() public TournamentCreated: EventEmitter<void> = new EventEmitter<void>();


  constructor(private tournamentService: TournamentService) {
    this.Tournament = new Tournament();
  }
  async onServerChange(guildId: string) {
    this.RolesLoaded = false;
    this.Tournament.RoleId = null;
    if (guildId) {
      let roles = await this.tournamentService.GetRoles(guildId);
      this.Roles = roles;
    }
    this.RolesLoaded = true;
  }
  async ngOnInit(): Promise<void> {
    let servers = await this.tournamentService.GetServers();
    this.Servers = servers;
  }

  public async CreateTournament() {
    await this.tournamentService.CreateTournament(this.Tournament);
    this.TournamentCreated.emit();
  }
}
