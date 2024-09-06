import { Component, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { NotificationItemComponent } from "../components/notification-item/notification-item.component";
import { EventBar } from '../Data/EventBar';
import { NgFor } from '@angular/common';
import { NotificationService } from '../Service/NotificationService';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    RouterOutlet,
    NotificationItemComponent,
    NgFor
  ],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent implements OnInit {
  constructor(private notificationService: NotificationService) { }
  ngOnInit(): void {
    this.notificationService.EventSource.subscribe(e => {
      this.Notifications.push(e);
    });
  }
  title = 'tournamentmanager';
  public Notifications: EventBar[] = [];

}
