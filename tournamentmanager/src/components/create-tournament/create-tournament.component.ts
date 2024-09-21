import { Component, EventEmitter, OnInit, Output } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Tournament } from '../../Data/Tournament';
import { NgFor, NgIf } from '@angular/common';
import { TournamentService } from '../../Service/TournamentsService';
import { DiscordServer } from '../../Data/DiscordServer';

@Component({
  selector: 'createTournament',
  standalone: true,
  imports: [
    FormsModule,
    NgIf,
    NgFor
  ],
  templateUrl: './create-tournament.component.html',
  styleUrl: './create-tournament.component.css'
})
export class CreateTournamentComponent implements OnInit {
  public Tournament: Tournament;
  public Servers: DiscordServer[] = [];

  @Output() public TournamentCreated: EventEmitter<void> = new EventEmitter<void>();


  constructor(private tournamentService: TournamentService) {
    this.Tournament = new Tournament();
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
