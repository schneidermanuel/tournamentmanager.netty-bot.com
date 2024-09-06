import { Component, Input } from '@angular/core';
import { NgIf } from '@angular/common';

@Component({
  selector: 'notification-item',
  standalone: true,
  imports: [
    NgIf
  ],
  templateUrl: './notification-item.component.html',
  styleUrl: './notification-item.component.css'
})
export class NotificationItemComponent {
  @Input()
  public Text: string;
  @Input()
  public Level: string;
  public Display: boolean;
  public Timer: number = 8000;



  constructor() {
    this.Display = true;
    let interval = setInterval(() => {
      this.Timer -= 10;
      if (this.Timer < 0) {
        clearInterval(interval);
        this.Display = false;
      }
    },
      10);
  }
}
