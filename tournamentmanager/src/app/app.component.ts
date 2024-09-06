import { Component, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { NotificationItemComponent } from "../components/notification-item/notification-item.component";
import { EventBar } from '../Data/EventBar';
import { NgFor } from '@angular/common';
import { AutoAnimateModule } from '@formkit/auto-animate/angular'

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    RouterOutlet,
    NotificationItemComponent,
    NgFor,
    AutoAnimateModule
  ],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent implements OnInit {
  ngOnInit(): void {
    this.Notifications.push(new EventBar("I", "This website is still under development"))
    this.Notifications.push(new EventBar("I", "This website is still under development"))
    this.Notifications.push(new EventBar("I", "This website is still under development"))
    this.Notifications.push(new EventBar("I", "This website is still under development"))
  }
  title = 'tournamentmanager';
  public Notifications: EventBar[] = [];

}
