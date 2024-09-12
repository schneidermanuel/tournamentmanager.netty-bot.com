import { Component, OnInit } from '@angular/core';
import { TournamentService } from '../../Service/TournamentsService';
import { TournamentlistitemComponent } from '../../components/tournamentlistitem/tournamentlistitem.component';
import { TournamentList } from '../../Data/TournamentList';
import { NgFor } from '@angular/common';

@Component({
  selector: 'app-tournament-list-view',
  standalone: true,
  imports: [
    TournamentlistitemComponent,
    NgFor
  ],
  templateUrl: './tournament-list-view.component.html',
  styleUrl: './tournament-list-view.component.css'
})
export class TournamentListViewComponent implements OnInit {
  public Tournaments: TournamentList;

  constructor(private tournamentService: TournamentService) {
    this.Tournaments = new TournamentList();
  }

  async ngOnInit(): Promise<void> {
    let list = await this.tournamentService.GetTournaments();
    this.Tournaments = list;
  }

}
