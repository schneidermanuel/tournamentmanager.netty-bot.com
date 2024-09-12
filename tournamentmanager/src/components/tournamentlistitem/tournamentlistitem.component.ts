import { Component, Input } from '@angular/core';
import { Tournament } from '../../Data/Tournament';
import { NgClass, NgFor, NgIf } from '@angular/common';

@Component({
  selector: 'tournamentlistitem',
  standalone: true,
  imports: [
    NgFor,
    NgIf,
    NgClass
  ],
  templateUrl: './tournamentlistitem.component.html',
  styleUrl: './tournamentlistitem.component.css'
})
export class TournamentlistitemComponent {
  @Input()
  public Tournament: Tournament;
}
