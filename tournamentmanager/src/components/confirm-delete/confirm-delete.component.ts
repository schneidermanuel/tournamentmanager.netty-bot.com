import { Component, ElementRef, EventEmitter, Input, OnInit, Output } from '@angular/core';
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
  @Input() public User: Player;
  @Input() public Code: string;
  @Output() public PlayerDeleted: EventEmitter<Player> = new EventEmitter<Player>;

  public async Delete(): Promise<void> {
    await this.tournamentService.DeletePlayer(this.User, this.Code);
    this.PlayerDeleted.emit(this.User);
  }
}
