import { Component, OnInit } from '@angular/core';
import { TournamentService } from '../../Service/TournamentsService';
import { TournamentlistitemComponent } from '../../components/tournamentlistitem/tournamentlistitem.component';

@Component({
  selector: 'app-tournament-list-view',
  standalone: true,
  imports: [
    TournamentlistitemComponent
  ],
  templateUrl: './tournament-list-view.component.html',
  styleUrl: './tournament-list-view.component.css'
})
export class TournamentListViewComponent implements OnInit {

  constructor(private tournamentService: TournamentService) { }

  async ngOnInit(): Promise<void> {
    await this.tournamentService.GetTournaments();
  }

}
