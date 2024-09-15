import { Component, Input, OnInit } from '@angular/core';
import { Tournament } from '../../Data/Tournament';
import { NgClass, NgFor, NgIf } from '@angular/common';
import { TournamentService } from '../../Service/TournamentsService';
import { RouterLink } from '@angular/router';

@Component({
  selector: 'tournamentlistitem',
  standalone: true,
  imports: [
    NgFor,
    NgIf,
    NgClass,
    RouterLink
  ],
  templateUrl: './tournamentlistitem.component.html',
  styleUrl: './tournamentlistitem.component.css'
})
export class TournamentlistitemComponent implements OnInit {
  @Input()
  public Tournament: Tournament;
  public ManageLink: string;

  constructor(private tournamentService: TournamentService) { }

  async ngOnInit(): Promise<void> {
    let data = await this.tournamentService.GetDetails(this.Tournament.Code);
    this.Tournament = data;
    this.ManageLink = "../manage/" + this.Tournament.Code;
  }

}
