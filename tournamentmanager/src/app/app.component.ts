import { Component, HostListener, OnInit } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { NotificationItemComponent } from "../components/notification-item/notification-item.component";
import { EventBar } from '../Data/EventBar';
import { NgFor, NgStyle } from '@angular/common';
import { NotificationService } from '../Service/NotificationService';

@Component({
  selector: 'app-root',
  standalone: true,
  imports: [
    RouterOutlet,
    NotificationItemComponent,
    NgFor,
    NgStyle
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
    this.gradientStyle = `radial-gradient(circle at center, var(--tw-gradient-from), var(--tw-gradient-to))`;
  }
  title = 'tournamentmanager';
  public Notifications: EventBar[] = [];
  gradientStyle = '';

  @HostListener('mousemove', ['$event'])
  onMouseMove(event: MouseEvent) {
    const x = event.clientX;
    const y = event.clientY;
    this.gradientStyle = `radial-gradient(circle at ${x}px ${y}px, var(--tw-gradient-from), var(--tw-gradient-to))`;
  }

}
