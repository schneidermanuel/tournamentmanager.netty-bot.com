import { Component, EventEmitter, Input, OnInit, Output } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Player } from '../../Data/Player';
import { NgIf } from '@angular/common';
import { TournamentService } from '../../Service/TournamentsService';

@Component({
  selector: 'editPlayer',
  standalone: true,
  imports: [
    FormsModule,
    NgIf
  ],
  templateUrl: './edit-player.component.html',
  styleUrl: './edit-player.component.css'
})
export class EditPlayerComponent implements OnInit {
  constructor(private tournamentService: TournamentService) {

  }
  ngOnInit(): void {
    this.Timestamp = this.convertToDateTimeLocal(this.User.Timestamp);
  }
  @Input() public User: Player;
  @Input() public Code: string;
  public Timestamp: string;
  @Output() close = new EventEmitter<void>();



  public async SaveChanges(): Promise<void> {
    this.close.emit();
    this.User.Timestamp = this.Timestamp;
    await this.tournamentService.UpdatePlayer(this.Code, this.User);
    this.User.Timestamp = this.convertToCustomTimestamp(this.User.Timestamp);
  }

  convertToDateTimeLocal(timestamp: string): string {
    const [date, time] = timestamp.split(', ');
    const [day, month, year] = date.split('.');

    const dateTimeLocal = `${year}-${this.pad(month)}-${this.pad(day)}T${time.substring(0, 5)}`;
    return dateTimeLocal;
  }

  convertToCustomTimestamp(dateTimeLocal: string): string {
    const [date, time] = dateTimeLocal.split('T');
    const [year, month, day] = date.split('-');

    const customTimestamp = `${this.pad(day)}.${this.pad(month)}.${year}, ${time}:00`;
    return customTimestamp;
  }

  pad(value: string | number): string {
    return value.toString().padStart(2, '0');
  }
}
