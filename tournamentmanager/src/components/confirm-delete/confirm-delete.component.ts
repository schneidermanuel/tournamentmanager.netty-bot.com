import { Component, ElementRef, Input, OnInit } from '@angular/core';
import { Player } from '../../Data/Player';
import { TournamentService } from '../../Service/TournamentsService';

@Component({
  selector: 'confirmDelete',
  standalone: true,
  imports: [],
  templateUrl: './confirm-delete.component.html',
  styleUrl: './confirm-delete.component.css'
})
export class ConfirmDeleteComponent {
  constructor(private tournamentService: TournamentService) {
  }
  @Input()
  public User: Player;

  public Delete(): void {
    console.log(this.User);
  }
}
