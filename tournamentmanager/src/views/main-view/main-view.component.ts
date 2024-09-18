import { NgIf, NgStyle } from '@angular/common';
import { Component, OnInit } from '@angular/core';
import { Authenticator } from '../../Service/Authenticator';
import { RouterLink } from '@angular/router';
import { GlobalState } from '../../Service/GlobalState';
import { NotificationService } from '../../Service/NotificationService';

@Component({
  selector: 'app-main-view',
  standalone: true,
  imports: [
    NgStyle,
    NgIf,
    RouterLink
  ],
  templateUrl: './main-view.component.html',
  styleUrl: './main-view.component.css'
})
export class MainViewComponent implements OnInit {
  public isAuthenticated: boolean = false;

  constructor(private authenticator: Authenticator, public GlobalState: GlobalState, private notificationService: NotificationService) { }
  gradientStyle = '';

  async ngOnInit() {
    this.isAuthenticated = await this.authenticator.IsAuthenticated();
  }

  public LogOut(): void {
    this.authenticator.LogOut();
    this.isAuthenticated = false;
    this.notificationService.ShowMessage("Logged out")
  }
}
