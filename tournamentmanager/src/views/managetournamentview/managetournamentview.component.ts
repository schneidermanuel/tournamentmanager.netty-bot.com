import { Component, ElementRef, OnInit } from '@angular/core';
import { TournamentService } from '../../Service/TournamentsService';
import { ActivatedRoute } from '@angular/router';
import { Tournament } from '../../Data/Tournament';
import { NgFor, NgIf } from '@angular/common';
import { ModifyStatusService } from '../../Service/ModifyStatusService';
import { ModifyStatusAction } from '../../Service/ModifyStatusAction';
import { EditPlayerComponent } from '../../components/edit-player/edit-player.component';

@Component({
  selector: 'managetournamentview',
  standalone: true,
  imports: [
    NgIf,
    NgFor,
    EditPlayerComponent
  ],
  templateUrl: './managetournamentview.component.html',
  styleUrl: './managetournamentview.component.css'
})
export class ManagetournamentviewComponent implements OnInit {
  public Tournament: Tournament;
  public ModifyStatusActions: ModifyStatusAction[];

  constructor(
    private tournamentService: TournamentService,
    private route: ActivatedRoute,
    private modifyStatusService: ModifyStatusService,
    private elementRef: ElementRef
  ) {
  }
  public ActionButtonClicked(newStauts: string) {
    console.log(newStauts);
  }

  async ngOnInit(): Promise<void> {
    let code = this.route.snapshot.paramMap.get('code');
    let tournament = await this.tournamentService.GetDetails(code);

    this.ModifyStatusActions = this.modifyStatusService.GetValidActions(tournament.Status);

    this.Tournament = tournament;
  }
  openModal(discordId: string) {
    const escapedId = `#\\3${discordId.charAt(0)} ${discordId.substring(1)}`;
    const modal = this.elementRef.nativeElement.querySelector(escapedId);
    if (modal) {
      modal.showModal();
    }
  }

}