import { Component, ElementRef, OnInit } from '@angular/core';
import { TournamentService } from '../../Service/TournamentsService';
import { TournamentlistitemComponent } from '../../components/tournamentlistitem/tournamentlistitem.component';
import { TournamentList } from '../../Data/TournamentList';
import { NgFor } from '@angular/common';
import { CreateTournamentComponent } from '../../components/create-tournament/create-tournament.component';

@Component({
  selector: 'app-tournament-list-view',
  standalone: true,
  imports: [
    TournamentlistitemComponent,
    NgFor,
    CreateTournamentComponent
  ],
  templateUrl: './tournament-list-view.component.html',
  styleUrl: './tournament-list-view.component.css'
})
export class TournamentListViewComponent implements OnInit {
  public Tournaments: TournamentList;

  constructor(private tournamentService: TournamentService, private elementRef: ElementRef) {
    this.Tournaments = new TournamentList();
  }

  async ngOnInit(): Promise<void> {
    let list = await this.tournamentService.GetTournaments();
    this.Tournaments = list;
  }
  public async TournamentCreated() {
    this.Tournaments = null;
    const modal = this.elementRef.nativeElement.querySelector("#CreateModal");
    console.log(modal);
    modal.close();
    let list = await this.tournamentService.GetTournaments();
    this.Tournaments = list;
  }
  public CreateTournament() {
    const modal = this.elementRef.nativeElement.querySelector("#CreateModal");
    if (modal) {
      modal.showModal();
    }
  }

}
